<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Projets;
use Illuminate\Http\Request;
use App\Http\Requests\ProjetRequest;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ProjetController extends Controller
{
    /**
     * Liste paginée des projets
     * - Admin : voit tous les projets
     * - Membre : voit uniquement les projets approuvés
     */
    public function index()
    {
        $user = auth()->user();
        $query = Projets::query();

        // Si l'utilisateur n'est pas admin, filtrer uniquement les projets approuvés
        if (!$user || $user->role !== 'administrateur') {
            $query->where('statut', 'approuve');
        }

        $projets = $query->orderBy('created_at', 'desc')->paginate(10);

        return response()->json([
            'message' => 'Liste des projets',
            'projets' => $projets
        ], 200);
    }

    /**
     * Création d’un projet
     */
    public function store(ProjetRequest $request)
    {
        $data = $request->validated();

        // Upload de l'image (optionnel)
        if ($request->hasFile('image_url')) {
            $image = $request->file('image_url');
            $filename = Str::uuid() . '.' . $image->getClientOriginalExtension();
            $image->storeAs('public/projets', $filename);
            $data['image_url'] = 'storage/projets/' . $filename;
        }

        $projet = Projets::create($data);

        return response()->json([
            'message' => 'Projet créé avec succès',
            'projet' => $projet
        ], 201);
    }

    /**
     * Détails d’un projet
     */
    public function show(Projets $projet)
    {
        return response()->json([
            'message' => 'Détails du projet',
            'projet' => $projet
        ], 200);
    }

    /**
     * Mise à jour d’un projet
     */
    public function update(ProjetRequest $request, Projets $projet)
    {
        $data = $request->validated();

        // Nouveau fichier image
        if ($request->hasFile('image_url')) {
            // Supprimer l’ancien si existe
            if ($projet->image_url) {
                $oldPath = str_replace('storage/', 'public/', $projet->image_url);
                Storage::delete($oldPath);
            }

            $image = $request->file('image_url');
            $filename = Str::uuid() . '.' . $image->getClientOriginalExtension();
            $image->storeAs('public/projets', $filename);
            $data['image_url'] = 'storage/projets/' . $filename;
        }

        $projet->update($data);

        return response()->json([
            'message' => 'Projet mis à jour avec succès',
            'projet' => $projet
        ], 200);
    }

    /**
     * Suppression d’un projet
     */
    public function destroy(Projets $projet)
    {
        // Supprimer l'image si existe
        if ($projet->image_url) {
            $oldPath = str_replace('storage/', 'public/', $projet->image_url);
            Storage::delete($oldPath);
        }

        $projet->delete();

        return response()->json([
            'message' => 'Projet supprimé avec succès'
        ], 200);
    }
    /**
 * Récupère les projets approuvés avec leurs dates de fin 
 * pour le compte à rebours sur l'accueil.
 */
public function getApprovedProjectsDeadlines()
{
    Log::info("=== Début de la gestion des échéances de projets ===");

    try {
        $now = now();
        $today = $now->toDateString(); // Pour comparer avec le format 'date' de ta BDD

        // 1. AUTOMATISATION : On passe à 'termine' les projets dont l'échéance est atteinte
        $projetsTermines = Projets::whereIn('statut', ['approuve', 'en_cours'])
            ->where('date_fin_prevue', '<=', $today)
            ->update([
                'statut' => 'termine',
                'date_fin_reelle' => $today // On enregistre la date de fin réelle
            ]);

        if ($projetsTermines > 0) {
            Log::info("Mise à jour automatique : $projetsTermines projet(s) passé(s) au statut 'termine'.");
        }

        // 2. RÉCUPÉRATION : On récupère les projets encore actifs pour l'affichage
        // On prend 'approuve' et 'en_cours' car ils ont des timers actifs
        $projets = Projets::whereIn('statut', ['approuve', 'en_cours'])
            ->where('date_fin_prevue', '>', $today)
            ->select('id', 'nom', 'date_fin_prevue', 'statut') 
            ->orderBy('date_fin_prevue', 'asc')
            ->get();

        Log::info("Projets actifs envoyés au frontend : " . $projets->count());

        return response()->json([
            'projets' => $projets,
            'server_time' => $now->toDateTimeString()
        ], 200);

    } catch (\Exception $e) {
        Log::error("ERREUR lors de la gestion des deadlines : " . $e->getMessage());
        return response()->json([
            'error' => 'Erreur lors de la mise à jour des projets',
            'details' => $e->getMessage()
        ], 500);
    }
}
}