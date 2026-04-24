<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Evenements;
use App\Models\Inscription_events;
use App\Models\Paiement;
use App\Services\CampayService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class EvenementPaiementController extends Controller
{
    private CampayService $campay;

    public function __construct(CampayService $campay)
    {
        $this->campay = $campay;
    }

    /**
     * Initier le paiement pour une inscription événement
     */
    public function payerEvenement(Request $request, string $code_evenement): JsonResponse
    {
        $membre = $request->user();

        // Vérification de l'inscription 
        $inscription = Inscription_events::where('code_membre', $membre->code_membre)
            ->where('code_evenement', $code_evenement)
            ->first();

        if (!$inscription) {
            return response()->json([
                'message' => 'Vous devez d\'abord vous inscrire à cet événement.'
            ], 404);
        }

        if ($inscription->statut_paiement === 'paye') {
            return response()->json([
                'message' => 'Cette inscription est déjà payée.'
            ], 422);
        }

        // Récupérer l'événement
        $evenement = Evenements::where('code_evenement', $code_evenement)->first();

        if (!$evenement || $evenement->frais_inscription <= 0) {
            return response()->json([
                'message' => 'Cet événement est gratuit, aucun paiement requis.'
            ], 422);
        }

        // Vérifier que le membre a un numéro de téléphone
        if (!$membre->telephone) {
            return response()->json([
                'message' => 'Veuillez ajouter un numéro de téléphone à votre profil pour effectuer le paiement.'
            ], 422);
        }

        // Générer référence unique
        $reference = $this->campay->generateReference('EVT');

        // Créer l'enregistrement paiement
        $codePaiement = 'PAY-' . date('Ymd') . '-' . strtoupper(Str::random(6));
        
        $paiement = Paiement::create([
            'code_paiement' => $codePaiement,
            'code_membre' => $membre->code_membre,
            'reference' => $reference,
            'montant' => $evenement->frais_inscription,
            'type' => 'inscription_evenement',
            'statut' => 'initie',
            'mode_paiement' => 'campay',
            'objet_relie_type' => 'Evenement',
            'objet_relie_code' => $code_evenement,
        ]);

        // Initialiser le paiement Campay
        $result = $this->campay->initializePayment([
            'email' => $membre->email,
            'amount' => (int) ($evenement->frais_inscription),
            'currency' => 'XAF',
            'reference' => $reference,
            'description' => "Inscription: {$evenement->titre}",
            'name' => "{$membre->prenom} {$membre->nom}",
            'phone' => $membre->telephone,
        ]);

        if (!$result['success']) {
            $paiement->update(['statut' => 'erreur']);
            
            return response()->json([
                'message' => $result['message'],
                'errors' => $result['errors'] ?? [],
            ], 422);
        }

        // Mettre à jour avec les infos Campay
        $paiement->update([
            'statut' => 'en_attente',
            'transaction_id' => $result['transaction']['reference'] ?? null,
            'details_paiement' => $result['transaction'] ?? null,
        ]);

        // Mettre à jour l'inscription
        $inscription->update([
            'reference_paiement' => $reference,
            'mode_paiement' => 'campay',
        ]);

        return response()->json([
            'message' => 'Paiement initié avec succès. Veuillez confirmer sur votre téléphone.',
            'reference' => $reference,
            'montant' => $evenement->frais_inscription,
            'evenement' => [
                'code' => $evenement->code_evenement,
                'titre' => $evenement->titre,
            ],
            'instructions' => 'Vous allez recevoir une notification sur votre téléphone pour confirmer le paiement.',
        ]);
    }

    /**
     * Vérifier le statut d'un paiement
     */
    public function verifierStatut(Request $request, string $reference): JsonResponse
    {
        $paiement = Paiement::where('reference', $reference)->first();

        if (!$paiement) {
            return response()->json([
                'message' => 'Paiement non trouvé.'
            ], 404);
        }

        // Si déjà finalisé, retourner le statut local
        if (in_array($paiement->statut, ['paye', 'annule', 'erreur'])) {
            return response()->json([
                'reference' => $reference,
                'statut' => $paiement->statut,
                'montant' => $paiement->montant,
                'date_paiement' => $paiement->date_paiement,
            ]);
        }

        // Sinon, vérifier auprès de Campay
        $result = $this->campay->verifyPayment($reference);

        if ($result['success']) {
            $nouveauStatut = $this->campay->mapStatus($result['status']);
            
            if ($nouveauStatut !== $paiement->statut) {
                $this->updatePaiementStatus($paiement, $nouveauStatut, $result['transaction']);
            }

            return response()->json([
                'reference' => $reference,
                'statut' => $nouveauStatut,
                'montant' => $paiement->montant,
                'date_paiement' => $paiement->date_paiement,
                'transaction' => $result['transaction'],
            ]);
        }

        return response()->json([
            'reference' => $reference,
            'statut' => $paiement->statut,
            'message' => $result['message'],
        ]);
    }

    /**
     * Mettre à jour le statut du paiement et de l'inscription liée
     */
    private function updatePaiementStatus(Paiement $paiement, string $statut, array $transaction = []): void
    {
        DB::transaction(function () use ($paiement, $statut, $transaction) {
            $paiement->update([
                'statut' => $statut,
                'details_paiement' => $transaction,
                'date_paiement' => $statut === 'paye' ? now() : null,
            ]);

            // Si c'est un paiement d'événement, mettre à jour l'inscription
            if ($paiement->type === 'inscription_evenement' && $paiement->objet_relie_code) {
                $inscription = Inscription_events::where('code_membre', $paiement->code_membre)
                    ->where('code_evenement', $paiement->objet_relie_code)
                    ->first();

                if ($inscription) {
                    $statutPaiement = $statut === 'paye' ? 'paye' : 
                        ($statut === 'annule' ? 'annule' : 'en_attente');

                    $inscription->update([
                        'statut_paiement' => $statutPaiement,
                        'montant_paye' => $statut === 'paye' ? $paiement->montant : null,
                        'reference_paiement' => $paiement->reference,
                    ]);

                    // TODO: Envoyer email de confirmation si payé
                }
            }

            Log::info('[EVENEMENT-PAIEMENT] Statut mis à jour', [
                'reference' => $paiement->reference,
                'statut' => $statut,
                'type' => $paiement->type,
            ]);
        });
    }

    /**
     * Historique des paiements du membre connecté
     */
    public function mesPaiements(Request $request): JsonResponse
    {
        $membre = $request->user();

        $paiements = Paiement::where('code_membre', $membre->code_membre)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'message' => 'Historique des paiements',
            'paiements' => $paiements,
        ]);
    }
}
