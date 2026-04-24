<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UploadRessourceRequest;
use App\Http\Resources\RessourceResource;
use App\Models\Ressource;
use App\Services\FileStorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Exception;

class RessourceController extends Controller
{
    protected $fileService;
    
    public function __construct(FileStorageService $fileService)
    {
        $this->fileService = $fileService;
    }
    
    /**
     * Liste des ressources avec filtrage et logs de sécurité.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        Log::info('Tentative de récupération de la liste des ressources.', [
            'user_id' => $user ? $user->id : 'Invité',
            'filters' => $request->all()
        ]);

        $query = Ressource::with('uploader');
        
        // --- FILTRAGE ---
        if ($request->has('categorie')) {
            $query->where('categorie', $request->categorie);
        }
        
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }
        
        if ($request->has('search')) {
            $query->where('titre', 'like', '%' . $request->search . '%');
        }
        
        // Exclure les ressources expirées
        $query->where(function($q) {
            $q->whereNull('date_expiration')
              ->orWhere('date_expiration', '>', now());
        });
        
        // --- GESTION DES PERMISSIONS (LOGGÉE) ---
        if (!$user) {
            Log::debug('Utilisateur non authentifié : filtrage vers ressources publiques uniquement.');
            $query->where('est_public', true);
        } elseif (!$user->est_admin) {
            Log::debug('Utilisateur membre (non-admin) : filtrage publiques + authentification requise.', ['user_id' => $user->id]);
            $query->where(function($q) {
                $q->where('est_public', true)
                  ->orWhere('necessite_authentification', true);
            });
        } else {
            Log::debug('Administrateur détecté : accès total aux ressources.', ['user_id' => $user->id]);
        }
        
        $ressources = $query->orderBy('date_publication', 'desc')->paginate(15);
        
        Log::info('Liste des ressources récupérée avec succès.', ['count' => $ressources->count()]);

        return RessourceResource::collection($ressources);
    }

    /**
     * Création d'une ressource (Admin).
     */
    public function store(UploadRessourceRequest $request)
    {
        $user = auth()->user();
        Log::info('Début de l\'upload d\'une nouvelle ressource.', [
            'admin_id' => $user->id,
            'titre' => $request->titre
        ]);

        try {
            // Stockage physique du fichier via le service
            $fileData = $this->fileService->uploadRessource(
                $request->file('fichier'),
                $request->categorie
            );

            Log::debug('Fichier stocké avec succès via FileStorageService.', ['path' => $fileData['path']]);
            
            // Création de l'entrée en base de données
            $ressource = Ressource::create([
                'titre' => $request->titre,
                'type' => $request->type,
                'categorie' => $request->categorie,
                'chemin_fichier' => $fileData['path'],
                'nom_fichier' => $fileData['nom_fichier'],
                'extension_fichier' => $fileData['extension'],
                'description' => $request->description,
                'date_publication' => now(),
                'date_expiration' => $request->date_expiration,
                'est_public' => $request->est_public ?? false,
                'necessite_authentification' => $request->necessite_authentification ?? true,
                'code_membre' => $user->code_membre,
                'nombre_telechargements' => 0,
            ]);

            Log::notice('Ressource créée en base de données.', ['id' => $ressource->id, 'slug' => $ressource->titre]);
            
            return new RessourceResource($ressource->load('uploader'));

        } catch (Exception $e) {
            Log::error('Erreur lors de la création de la ressource.', [
                'error' => $e->getMessage(),
                'admin_id' => $user->id
            ]);
            return response()->json(['message' => 'Erreur lors de l\'upload.'], 500);
        }
    }

    /**
     * Affichage d'une ressource spécifique.
     */
    public function show($id)
    {
        $ressource = Ressource::with('uploader')->findOrFail($id);
        $user = auth()->user();
        
        Log::debug('Consultation du détail d\'une ressource.', ['ressource_id' => $id, 'user_id' => $user ? $user->id : 'Invité']);

        if (!$ressource->est_public && !$user) {
            Log::warning('Accès refusé : Tentative de consultation sans authentification.', ['ressource_id' => $id]);
            return response()->json([
                'message' => 'Authentification requise pour accéder à cette ressource.'
            ], 401);
        }
        
        return new RessourceResource($ressource);
    }

    /**
     * Téléchargement d'une ressource (Logging critique pour l'audit).
     */
    public function download($id)
    {
        $ressource = Ressource::findOrFail($id);
        $user = auth()->user();
        
        Log::info('Demande de téléchargement reçue.', [
            'ressource_id' => $id,
            'user_id' => $user ? $user->id : 'Invité',
            'ip' => request()->ip()
        ]);

        // Vérifier permissions
        if ($ressource->necessite_authentification && !$user) {
            Log::warning('Téléchargement bloqué : Authentification manquante.', ['ressource_id' => $id]);
            return response()->json([
                'message' => 'Authentification requise pour télécharger cette ressource.'
            ], 401);
        }
        
        // Incrémentation du compteur
        $ressource->increment('nombre_telechargements');
        
        // Chemin complet
        $fullPath = $this->fileService->getFullPath($ressource->chemin_fichier, 'ressources');
        
        if (!file_exists($fullPath)) {
            Log::critical('Fichier absent du disque malgré l\'entrée en BDD !', [
                'ressource_id' => $id,
                'path' => $fullPath
            ]);
            return response()->json([
                'message' => 'Fichier introuvable sur le serveur.'
            ], 404);
        }

        Log::info('Téléchargement lancé avec succès.', ['ressource_id' => $id, 'file' => $ressource->nom_fichier]);
        
        return response()->download($fullPath, $ressource->nom_fichier);
    }
}