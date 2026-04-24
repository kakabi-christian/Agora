<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ParticipationProjetRequest;
use App\Http\Resources\ParticipationProjetResource;
use App\Models\Participation_projets;
use App\Models\Projets;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ParticipationProjetController extends Controller
{
    /**
     * S'inscrire à un projet (membre connecté)
     */
    public function participer(ParticipationProjetRequest $request, int $projet_id): JsonResponse
    {
        $membre = $request->user();

        // Vérifier que le projet existe
        $projet = Projets::find($projet_id);

        if (!$projet) {
            return response()->json([
                'message' => 'Projet non trouvé.'
            ], 404);
        }

        // Vérifier que le projet accepte des participants
        if (!in_array($projet->statut, ['approuve', 'en_cours'])) {
            return response()->json([
                'message' => 'Ce projet n\'accepte plus de participants.'
            ], 422);
        }

        // Vérifier si le membre participe déjà
        $dejaParticipant = Participation_projets::where('code_membre', $membre->code_membre)
            ->where('projet_id', $projet_id)
            ->exists();

        if ($dejaParticipant) {
            return response()->json([
                'message' => 'Vous participez déjà à ce projet.'
            ], 409);
        }

        $data = $request->validated();

        // Créer la participation
        $participation = Participation_projets::create([
            'code_membre' => $membre->code_membre,
            'projet_id' => $projet_id,
            'date_participation' => now(),
            'role' => $data['role'] ?? 'participant',
            'statut' => 'actif',
            'heures_contribuees' => 0,
            'taches' => $data['taches'] ?? null,
            'competences_apportees' => $data['competences_apportees'] ?? null,
        ]);

        $participation->load('projet');

        return response()->json([
            'message' => 'Participation enregistrée avec succès.',
            'participation' => new ParticipationProjetResource($participation),
        ], 201);
    }


    /**
     * Quitter un projet (membre connecté)
     */
    public function quitter(Request $request, int $projet_id): JsonResponse
    {
        $membre = $request->user();

        $participation = Participation_projets::where('code_membre', $membre->code_membre)
            ->where('projet_id', $projet_id)
            ->first();

        if (!$participation) {
            return response()->json([
                'message' => 'Vous ne participez pas à ce projet.'
            ], 404);
        }

        // Marquer comme inactif plutôt que supprimer (pour garder l'historique)
        $participation->update(['statut' => 'inactif']);

        return response()->json([
            'message' => 'Vous avez quitté le projet.',
            'heures_contribuees' => $participation->heures_contribuees,
        ]);
    }

    /**
     * Voir mes participations aux projets
     */
    public function mesParticipations(Request $request): JsonResponse
    {
        $membre = $request->user();

        $participations = Participation_projets::where('code_membre', $membre->code_membre)
            ->with('projet')
            ->orderBy('date_participation', 'desc')
            ->paginate(10);

        // Statistiques
        $stats = [
            'total_projets' => Participation_projets::where('code_membre', $membre->code_membre)->count(),
            'projets_actifs' => Participation_projets::where('code_membre', $membre->code_membre)
                ->where('statut', 'actif')->count(),
            'heures_totales' => Participation_projets::where('code_membre', $membre->code_membre)
                ->sum('heures_contribuees'),
        ];

        return response()->json([
            'message' => 'Mes participations aux projets',
            'participations' => ParticipationProjetResource::collection($participations),
            'stats' => $stats,
            'pagination' => [
                'total' => $participations->total(),
                'per_page' => $participations->perPage(),
                'current_page' => $participations->currentPage(),
                'last_page' => $participations->lastPage(),
            ],
        ]);
    }

    /**
     * Mettre à jour mes heures sur un projet
     */
    public function logHeures(Request $request, int $projet_id): JsonResponse
    {
        $request->validate([
            'heures' => 'required|integer|min:1|max:100',
            'description' => 'nullable|string|max:255',
        ]);

        $membre = $request->user();

        $participation = Participation_projets::where('code_membre', $membre->code_membre)
            ->where('projet_id', $projet_id)
            ->where('statut', 'actif')
            ->first();

        if (!$participation) {
            return response()->json([
                'message' => 'Participation active non trouvée.'
            ], 404);
        }

        $participation->increment('heures_contribuees', $request->heures);

        return response()->json([
            'message' => 'Heures enregistrées.',
            'heures_ajoutees' => $request->heures,
            'heures_totales' => $participation->heures_contribuees,
        ]);
    }

    /**
     * [ADMIN] Liste des participants d'un projet
     */
    public function listeParProjet(int $projet_id): JsonResponse
    {
        $projet = Projets::find($projet_id);

        if (!$projet) {
            return response()->json([
                'message' => 'Projet non trouvé.'
            ], 404);
        }

        $participations = Participation_projets::where('projet_id', $projet_id)
            ->with('membre')
            ->orderBy('date_participation', 'asc')
            ->get();

        $stats = [
            'total_participants' => $participations->count(),
            'participants_actifs' => $participations->where('statut', 'actif')->count(),
            'heures_totales' => $participations->sum('heures_contribuees'),
        ];

        return response()->json([
            'projet' => [
                'id' => $projet->id,
                'nom' => $projet->nom,
                'statut' => $projet->statut,
            ],
            'statistiques' => $stats,
            'participations' => ParticipationProjetResource::collection($participations),
        ]);
    }

    /**
     * [ADMIN] Modifier le statut/rôle d'un participant
     */
    public function updateParticipation(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'role' => 'nullable|string|max:100',
            'statut' => 'nullable|in:actif,inactif,suspendu',
            'heures_contribuees' => 'nullable|integer|min:0',
        ]);

        $participation = Participation_projets::find($id);

        if (!$participation) {
            return response()->json([
                'message' => 'Participation non trouvée.'
            ], 404);
        }

        $participation->update($request->only(['role', 'statut', 'heures_contribuees']));

        return response()->json([
            'message' => 'Participation mise à jour.',
            'participation' => new ParticipationProjetResource($participation->load(['projet', 'membre'])),
        ]);
    }

    /**
     * [ADMIN] Ajouter un membre à un projet
     */
    public function ajouterParticipant(Request $request, int $projet_id): JsonResponse
    {
        $request->validate([
            'code_membre' => 'required|exists:membres,code_membre',
            'role' => 'nullable|string|max:100',
        ]);

        $projet = Projets::find($projet_id);

        if (!$projet) {
            return response()->json([
                'message' => 'Projet non trouvé.'
            ], 404);
        }

        // Vérifier si déjà participant
        $existe = Participation_projets::where('code_membre', $request->code_membre)
            ->where('projet_id', $projet_id)
            ->exists();

        if ($existe) {
            return response()->json([
                'message' => 'Ce membre participe déjà au projet.'
            ], 409);
        }

        $participation = Participation_projets::create([
            'code_membre' => $request->code_membre,
            'projet_id' => $projet_id,
            'date_participation' => now(),
            'role' => $request->role ?? 'participant',
            'statut' => 'actif',
            'heures_contribuees' => 0,
        ]);

        return response()->json([
            'message' => 'Participant ajouté au projet.',
            'participation' => new ParticipationProjetResource($participation->load(['projet', 'membre'])),
        ], 201);
    }

    /**
     * [ADMIN] Retirer un participant d'un projet
     */
    public function retirerParticipant(int $id): JsonResponse
    {
        $participation = Participation_projets::find($id);

        if (!$participation) {
            return response()->json([
                'message' => 'Participation non trouvée.'
            ], 404);
        }

        $participation->delete();

        return response()->json([
            'message' => 'Participant retiré du projet.'
        ]);
    }
}
