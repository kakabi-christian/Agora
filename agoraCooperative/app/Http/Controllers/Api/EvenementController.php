<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Evenements;
use Illuminate\Http\Request;
use App\Http\Requests\EvenementRequest;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class EvenementController extends Controller
{
    /**
     * Liste paginée des événements
     */
    public function index()
    {
        $evenements = Evenements::orderBy('date_debut', 'desc')->paginate(10);

        return response()->json([
            'message' => 'Liste des événements',
            'evenements' => $evenements
        ], 200);
    }

    /**
     * CRÉATION avec génération de code et Logs
     */
    public function store(EvenementRequest $request)
    {
        Log::info("=== DÉBUT CRÉATION ÉVÉNEMENT ===");
        $data = $request->validated();
        Log::info("Données validées :", $data);

        try {
            $data['code_evenement'] = $this->generateUniqueCode();

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $filename = Str::uuid() . '.' . $image->getClientOriginalExtension();
                $image->storeAs('public/evenements', $filename);
                $data['image_url'] = 'storage/evenements/' . $filename;
                Log::info("Image enregistrée : " . $data['image_url']);
            }

            $data['paiement_obligatoire'] = $request->boolean('paiement_obligatoire');

            $evenement = Evenements::create($data);
            Log::info("Succès ! Événement créé : " . $evenement->code_evenement);

            return response()->json([
                'message' => 'Événement créé avec succès',
                'evenement' => $evenement
            ], 201);
        } catch (\Exception $e) {
            Log::error("Erreur Store : " . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de la création'], 500);
        }
    }

    /**
     * MISE À JOUR (Update)
     * Note: Laravel gère mal le FormData avec la méthode PUT, 
     * utilise POST avec le champ _method=PUT ou reste en POST pour les uploads.
     */
    public function update(Request $request, string $code_evenement)
    {
        Log::info("=== DÉBUT MISE À JOUR ÉVÉNEMENT : $code_evenement ===");
        
        $evenement = Evenements::where('code_evenement', $code_evenement)->first();

        if (!$evenement) {
            return response()->json(['message' => 'Événement non trouvé'], 404);
        }

        // Pour l'update, on valide manuellement ou on utilise une Request différente 
        // car le code_evenement doit ignorer l'ID actuel pour l'unique
        $data = $request->all();

        try {
            if ($request->hasFile('image')) {
                // Supprimer l'ancienne image si elle existe
                if ($evenement->image_url) {
                    Storage::delete(str_replace('storage/', 'public/', $evenement->image_url));
                }

                $image = $request->file('image');
                $filename = Str::uuid() . '.' . $image->getClientOriginalExtension();
                $image->storeAs('public/evenements', $filename);
                $data['image_url'] = 'storage/evenements/' . $filename;
            }

            if (isset($data['paiement_obligatoire'])) {
                $data['paiement_obligatoire'] = $request->boolean('paiement_obligatoire');
            }

            $evenement->update($data);
            Log::info("Mise à jour réussie pour : " . $evenement->code_evenement);

            return response()->json([
                'message' => 'Événement mis à jour avec succès',
                'evenement' => $evenement
            ], 200);
        } catch (\Exception $e) {
            Log::error("Erreur Update : " . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de la mise à jour'], 500);
        }
    }

    /**
     * SUPPRESSION (Destroy)
     */
    public function destroy(string $code_evenement)
    {
        Log::info("=== TENTATIVE SUPPRESSION : $code_evenement ===");
        
        $evenement = Evenements::where('code_evenement', $code_evenement)->first();

        if (!$evenement) {
            Log::warning("Échec suppression : Événement $code_evenement introuvable");
            return response()->json(['message' => 'Événement non trouvé'], 404);
        }

        try {
            // Supprimer l'image physique
            if ($evenement->image_url) {
                Storage::delete(str_replace('storage/', 'public/', $evenement->image_url));
            }

            $evenement->delete();
            Log::info("Événement $code_evenement supprimé avec succès.");

            return response()->json(['message' => 'Événement supprimé avec succès'], 200);
        } catch (\Exception $e) {
            Log::error("Erreur Destroy : " . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de la suppression'], 500);
        }
    }

    /**
     * Détails d'un événement
     */
    public function show(string $code_evenement)
    {
        $evenement = Evenements::where('code_evenement', $code_evenement)->first();
        return $evenement 
            ? response()->json(['evenement' => $evenement], 200)
            : response()->json(['message' => 'Non trouvé'], 404);
    }

    /**
     * Automatisation des statuts (Upcoming)
     */
    public function getUpcomingEventsWithAutoStatus()
    {
        try {
            $now = now();
            // Auto-update vers 'termine'
            Evenements::where('date_fin', '<', $now)
                ->whereNotIn('statut', ['termine', 'annule'])
                ->update(['statut' => 'termine']);

            $evenements = Evenements::whereIn('statut', ['planifie', 'en_cours'])
                ->where('date_fin', '>', $now)
                ->orderBy('date_debut', 'asc')
                ->get();

            return response()->json(['evenements' => $evenements], 200);
        } catch (\Exception $e) {
            Log::error("Erreur Upcoming : " . $e->getMessage());
            return response()->json(['error' => 'Erreur serveur'], 500);
        }
    }

    /**
     * Helper pour code unique
     */
    private function generateUniqueCode()
    {
        do {
            $code = 'EVT-' . strtoupper(Str::random(8));
        } while (Evenements::where('code_evenement', $code)->exists());
        return $code;
    }
}
// http/controllers/Api/EvenementController.php