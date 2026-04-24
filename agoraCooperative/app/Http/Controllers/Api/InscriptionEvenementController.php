<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\InscriptionEvenementRequest;
use App\Http\Resources\InscriptionEvenementResource;
use App\Mail\InscriptionAnnulation;
use App\Mail\InscriptionConfirmation;
use App\Models\Evenements;
use App\Models\Inscription_events;
use App\Services\PdfService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class InscriptionEvenementController extends Controller
{
    private PdfService $pdfService;

    public function __construct(PdfService $pdfService)
    {
        $this->pdfService = $pdfService;
    }

    /**
     * Inscrire le membre connecté à un événement
     */
    public function inscrire(InscriptionEvenementRequest $request, string $code_evenement): JsonResponse
    {
        $membre = $request->user();

        // Vérifier que l'événement existe
        $evenement = Evenements::where('code_evenement', $code_evenement)->first();

        if (!$evenement) {
            return response()->json([
                'message' => 'Événement non trouvé.'
            ], 404);
        }

        // Vérifier que l'événement n'est pas annulé ou terminé
        if (in_array($evenement->statut, ['annule', 'termine'])) {
            return response()->json([
                'message' => 'Cet événement n\'est plus disponible pour inscription.'
            ], 422);
        }

        // Vérifier si le membre est déjà inscrit
        $dejaInscrit = Inscription_events::where('code_membre', $membre->code_membre)
            ->where('code_evenement', $code_evenement)
            ->exists();

        if ($dejaInscrit) {
            return response()->json([
                'message' => 'Vous êtes déjà inscrit à cet événement.'
            ], 409);
        }

        // Vérifier les places disponibles
        if ($evenement->places_disponibles !== null) {
            $inscriptionsCount = Inscription_events::where('code_evenement', $code_evenement)
                ->whereIn('statut_participation', ['inscrit', 'present'])
                ->count();

            if ($inscriptionsCount >= $evenement->places_disponibles) {
                return response()->json([
                    'message' => 'Plus de places disponibles pour cet événement.'
                ], 422);
            }
        }

        // Déterminer le statut de paiement initial
        $statutPaiement = 'en_attente';
        if ($evenement->frais_inscription == 0 || !$evenement->paiement_obligatoire) {
            $statutPaiement = 'paye';
        }

        // Créer l'inscription
        $inscription = Inscription_events::create([
            'code_membre' => $membre->code_membre,
            'code_evenement' => $code_evenement,
            'date_inscription' => now(),
            'statut_paiement' => $statutPaiement,
            'statut_participation' => 'inscrit',
            'montant_paye' => $statutPaiement === 'paye' ? 0 : null,
            'commentaires' => $request->commentaires,
        ]);

        $inscription->load(['evenement', 'membre']);

        // Envoyer email de confirmation avec PDF
        try {
            Mail::to($membre->email)->send(new InscriptionConfirmation($inscription, $evenement, $membre));
        } catch (\Exception $e) {
            // Log l'erreur mais ne bloque pas l'inscription
            \Log::error('Erreur envoi email inscription: ' . $e->getMessage());
        }

        return response()->json([
            'message' => 'Inscription réussie.',
            'inscription' => new InscriptionEvenementResource($inscription),
            'paiement_requis' => $statutPaiement === 'en_attente',
            'montant_a_payer' => $statutPaiement === 'en_attente' ? $evenement->frais_inscription : 0,
        ], 201);
    }

    /**
     * Annuler l'inscription du membre connecté
     */
    public function annuler(Request $request, string $code_evenement): JsonResponse
    {
        $membre = $request->user();

        $inscription = Inscription_events::where('code_membre', $membre->code_membre)
            ->where('code_evenement', $code_evenement)
            ->first();

        if (!$inscription) {
            return response()->json([
                'message' => 'Inscription non trouvée.'
            ], 404);
        }

        // Vérifier si l'événement n'a pas déjà commencé
        $evenement = Evenements::where('code_evenement', $code_evenement)->first();
        if ($evenement && $evenement->date_debut <= now()) {
            return response()->json([
                'message' => 'Impossible d\'annuler une inscription pour un événement déjà commencé.'
            ], 422);
        }

        // Si paiement effectué, marquer comme remboursé
        if ($inscription->statut_paiement === 'paye' && $inscription->montant_paye > 0) {
            $inscription->update([
                'statut_paiement' => 'rembourse',
                'statut_participation' => 'absent',
            ]);

            // Envoyer email d'annulation
            try {
                Mail::to($membre->email)->send(new InscriptionAnnulation($inscription, $evenement, $membre));
            } catch (\Exception $e) {
                \Log::error('Erreur envoi email annulation: ' . $e->getMessage());
            }

            return response()->json([
                'message' => 'Inscription annulée. Un remboursement sera traité.',
                'remboursement' => true,
                'montant_rembourse' => $inscription->montant_paye,
            ]);
        }

        // Envoyer email d'annulation
        try {
            Mail::to($membre->email)->send(new InscriptionAnnulation($inscription, $evenement, $membre));
        } catch (\Exception $e) {
            \Log::error('Erreur envoi email annulation: ' . $e->getMessage());
        }

        $inscription->delete();

        return response()->json([
            'message' => 'Inscription annulée avec succès.'
        ]);
    }

    /**
     * Voir les inscriptions du membre connecté
     */
    public function mesInscriptions(Request $request): JsonResponse
    {
        $membre = $request->user();

        $inscriptions = Inscription_events::where('code_membre', $membre->code_membre)
            ->with('evenement')
            ->orderBy('date_inscription', 'desc')
            ->paginate(10);

        return response()->json([
            'message' => 'Mes inscriptions',
            'inscriptions' => InscriptionEvenementResource::collection($inscriptions),
            'pagination' => [
                'current_page' => $inscriptions->currentPage(),
                'last_page' => $inscriptions->lastPage(),
                'per_page' => $inscriptions->perPage(),
                'total' => $inscriptions->total(),
            ],
        ]);
    }

    /**
     * Voir le statut d'inscription pour un événement
     */
    public function statut(Request $request, string $code_evenement): JsonResponse
    {
        $membre = $request->user();

        $inscription = Inscription_events::where('code_membre', $membre->code_membre)
            ->where('code_evenement', $code_evenement)
            ->with('evenement')
            ->first();

        if (!$inscription) {
            return response()->json([
                'inscrit' => false,
                'message' => 'Non inscrit à cet événement.'
            ]);
        }

        return response()->json([
            'inscrit' => true,
            'inscription' => new InscriptionEvenementResource($inscription),
        ]);
    }

    /**
     * [ADMIN] Lister toutes les inscriptions d'un événement
     */
    public function listeParEvenement(string $code_evenement): JsonResponse
    {
        $evenement = Evenements::where('code_evenement', $code_evenement)->first();

        if (!$evenement) {
            return response()->json([
                'message' => 'Événement non trouvé.'
            ], 404);
        }

        $inscriptions = Inscription_events::where('code_evenement', $code_evenement)
            ->with('membre')
            ->orderBy('date_inscription', 'asc')
            ->get();

        $stats = [
            'total_inscrits' => $inscriptions->count(),
            'places_disponibles' => $evenement->places_disponibles,
            'places_restantes' => $evenement->places_disponibles 
                ? max(0, $evenement->places_disponibles - $inscriptions->whereIn('statut_participation', ['inscrit', 'present'])->count())
                : null,
            'paiements_en_attente' => $inscriptions->where('statut_paiement', 'en_attente')->count(),
            'paiements_confirmes' => $inscriptions->where('statut_paiement', 'paye')->count(),
        ];

        return response()->json([
            'evenement' => [
                'code_evenement' => $evenement->code_evenement,
                'titre' => $evenement->titre,
                'date_debut' => $evenement->date_debut,
            ],
            'statistiques' => $stats,
            'inscriptions' => InscriptionEvenementResource::collection($inscriptions),
        ]);
    }

    /**
     * [ADMIN] Modifier le statut de participation
     */
    public function updateStatut(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'statut_participation' => 'required|in:inscrit,present,absent',
        ]);

        $inscription = Inscription_events::find($id);

        if (!$inscription) {
            return response()->json([
                'message' => 'Inscription non trouvée.'
            ], 404);
        }

        // Charger l'événement pour vérifier les règles de paiement
        $evenement = Evenements::where('code_evenement', $inscription->code_evenement)->first();

        if (!$evenement) {
            return response()->json([
                'message' => 'Événement non trouvé.'
            ], 404);
        }

        // VALIDATION : Vérifier le paiement si nécessaire
        // Seulement si on marque "présent" ET que le paiement est obligatoire
        if ($request->statut_participation === 'present' && 
            $evenement->paiement_obligatoire && 
            $evenement->frais_inscription > 0) {
            
            if ($inscription->statut_paiement !== 'paye') {
                // Logger la tentative de marquage sans paiement
                \Log::warning('Tentative de marquage présence sans paiement confirmé', [
                    'admin_code' => $request->user()->code_membre,
                    'admin_email' => $request->user()->email,
                    'inscription_id' => $inscription->id,
                    'membre_code' => $inscription->code_membre,
                    'evenement_code' => $evenement->code_evenement,
                    'evenement_titre' => $evenement->titre,
                    'statut_paiement' => $inscription->statut_paiement,
                    'montant_requis' => $evenement->frais_inscription,
                ]);

                return response()->json([
                    'message' => 'Le paiement doit être confirmé avant de marquer la présence.',
                    'details' => [
                        'statut_paiement_actuel' => $inscription->statut_paiement,
                        'montant_a_payer' => $evenement->frais_inscription,
                        'paiement_obligatoire' => true,
                    ],
                ], 422);
            }
        }

        // Mise à jour autorisée
        $inscription->update([
            'statut_participation' => $request->statut_participation,
        ]);

        // Logger la mise à jour réussie
        \Log::info('Statut de participation mis à jour', [
            'admin_code' => $request->user()->code_membre,
            'inscription_id' => $inscription->id,
            'membre_code' => $inscription->code_membre,
            'ancien_statut' => $inscription->getOriginal('statut_participation'),
            'nouveau_statut' => $request->statut_participation,
            'evenement_code' => $evenement->code_evenement,
        ]);

        return response()->json([
            'message' => 'Statut mis à jour avec succès.',
            'inscription' => new InscriptionEvenementResource($inscription->load(['evenement', 'membre'])),
        ]);
    }

    /**
     * Télécharger la confirmation d'inscription en PDF
     */
    public function telechargerConfirmation(Request $request, string $code_evenement)
    {
        $membre = $request->user();

        $inscription = Inscription_events::where('code_membre', $membre->code_membre)
            ->where('code_evenement', $code_evenement)
            ->first();

        if (!$inscription) {
            return response()->json(['message' => 'Inscription non trouvée.'], 404);
        }

        return $this->pdfService->telechargerConfirmationInscription($inscription);
    }

    /**
     * [ADMIN] Vérifier un QR code d'inscription
     */
    public function verifierQrCode(Request $request): JsonResponse
    {
        $request->validate([
            'qr_data' => 'required|string',
        ]);

        try {
            $data = json_decode($request->qr_data, true);

            if (!$data || !isset($data['inscription_id'], $data['hash'])) {
                return response()->json([
                    'valide' => false,
                    'message' => 'QR code invalide ou corrompu.'
                ], 400);
            }

            // Vérifier le hash pour s'assurer que le QR code n'a pas été falsifié
            $expectedHash = hash('sha256', $data['inscription_id'] . $data['code_membre'] . config('app.key'));
            
            if ($data['hash'] !== $expectedHash) {
                \Log::warning('Tentative de scan avec QR code falsifié', [
                    'admin_code' => $request->user()->code_membre,
                    'inscription_id' => $data['inscription_id'] ?? 'unknown',
                ]);

                return response()->json([
                    'valide' => false,
                    'message' => 'QR code falsifié ou invalide.'
                ], 400);
            }

            // Récupérer l'inscription
            $inscription = Inscription_events::with(['evenement', 'membre'])
                ->find($data['inscription_id']);

            if (!$inscription) {
                return response()->json([
                    'valide' => false,
                    'message' => 'Inscription non trouvée.'
                ], 404);
            }

            // Vérifier que l'inscription correspond bien aux données du QR code
            if ($inscription->code_membre !== $data['code_membre'] || 
                $inscription->code_evenement !== $data['code_evenement']) {
                return response()->json([
                    'valide' => false,
                    'message' => 'Les données du QR code ne correspondent pas à l\'inscription.'
                ], 400);
            }

            // Logger la vérification
            \Log::info('QR code vérifié avec succès', [
                'admin_code' => $request->user()->code_membre,
                'inscription_id' => $inscription->id,
                'membre_code' => $inscription->code_membre,
                'evenement_code' => $inscription->code_evenement,
            ]);

            return response()->json([
                'valide' => true,
                'message' => 'QR code valide.',
                'inscription' => new InscriptionEvenementResource($inscription),
            ]);

        } catch (\Exception $e) {
            \Log::error('Erreur lors de la vérification du QR code', [
                'error' => $e->getMessage(),
                'admin_code' => $request->user()->code_membre,
            ]);

            return response()->json([
                'valide' => false,
                'message' => 'Erreur lors de la vérification du QR code.'
            ], 500);
        }
    }
}
