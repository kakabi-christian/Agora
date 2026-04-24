<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DonRequest;
use App\Models\Don;
use App\Services\CampayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
// AJOUTE CET IMPORT EN HAUT DU FICHIER
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected $campayService;

    public function __construct(CampayService $campayService)
    {
        $this->campayService = $campayService;
    }

    /**
     * Étape 1 : Initialiser le don et demander le paiement (Collect)
     */
    public function store(DonRequest $request)
    {
        Log::info('=== DÉBUT INITIALISATION DON ===');

        try {
            $don = Don::create([
                'nom_donateur' => $request->nom_donateur,
                'email_donateur' => $request->email_donateur,
                'telephone' => $request->telephone,
                'type' => $request->type,
                'montant' => $request->montant,
                'message_donateur' => $request->message_donateur,
                'anonyme' => $request->anonyme ?? false,
                'mode_paiement' => 'Campay',
                'statut_paiement' => 'en_attente',
                'date_don' => now(),
            ]);

            Log::info("Don créé en BDD ID: {$don->id}");

            $description = 'Don '.$don->type.' par '.$don->nom_donateur;
            $campayResponse = $this->campayService->collect(
                $don->montant,
                $don->telephone,
                $description
            );

            if (isset($campayResponse['reference'])) {
                $don->update(['reference_paiement' => $campayResponse['reference']]);

                return response()->json(['success' => true, 'data' => $campayResponse], 201);
            }

            return response()->json(['success' => false, 'error' => $campayResponse], 400);

        } catch (\Exception $e) {
            Log::error('CRASH STORE : '.$e->getMessage());

            return response()->json(['success' => false, 'message' => 'Erreur interne.'], 500);
        }
    }

    /**
     * Étape 2 : Recevoir la confirmation de Campay (Webhook)
     */
    public function handleWebhook(Request $request)
    {
        Log::info('=== WEBHOOK CAMPAY REÇU ===');
        Log::info('Données reçues :', $request->all());

        // Campay envoie généralement 'reference' et 'status'
        $reference = $request->input('reference');
        $status = $request->input('status'); // 'SUCCESSFUL' ou 'FAILED'

        // 1. Trouver le don correspondant
        $don = Don::where('reference_paiement', $reference)->first();

        if (! $don) {
            Log::error("Webhook : Référence {$reference} introuvable en BDD.");

            return response()->json(['message' => 'Transaction non trouvée'], 404);
        }

        // 2. Vérifier si le don est déjà traité pour éviter les doublons
        if ($don->statut_paiement === 'succes') {
            return response()->json(['message' => 'Déjà traité'], 200);
        }

        // 3. Mise à jour selon le statut reçu
        if ($status === 'SUCCESSFUL') {
            $don->update([
                'statut_paiement' => 'succes',
                'date_don' => now(),
            ]);
            Log::info("✅ Don ID {$don->id} passé en SUCCÈS.");

            // C'est ici que tu peux déclencher d'autres actions (Email de remerciement, etc.)
        } else {
            $don->update(['statut_paiement' => 'echec']);
            Log::warning("❌ Don ID {$don->id} marqué comme ÉCHEC.");
        }

        return response()->json(['status' => 'ok'], 200);
    }

    /**
     * Étape 3 : Retrait MANUEL (Payout)
     * Utile si tu préfères cumuler l'argent et le retirer plus tard
     */
    /**
     * Étape 3 : Retrait MANUEL (Payout) sécurisé par mot de passe
     */
    public function payoutToAdmin(Request $request)
    {
        // ID unique pour suivre cette transaction précise dans les logs
        $logId = bin2hex(random_bytes(4));
        Log::info("[$logId] === DÉBUT PROCESSUS RETRAIT ADMIN ===");

        // 1. Validation
        $request->validate([
            'amount' => 'required|numeric|min:5',
            'password' => 'required',
        ]);

        // 2. Récupérer l'admin
        $admin = auth()->user();
        if (! $admin) {
            Log::warning("[$logId] Tentative de retrait sans session valide.");

            return response()->json(['success' => false, 'message' => 'Session expirée ou non authentifiée'], 401);
        }

        Log::info("[$logId] Admin identifié : {$admin->code_membre} | Email : {$admin->email}");

        // 3. Vérification du mot de passe
        if (! Hash::check($request->password, $admin->mot_de_passe)) {
            Log::warning("[$logId] Échec mot de passe pour l'admin : {$admin->code_membre}");

            return response()->json([
                'success' => false,
                'message' => 'Le mot de passe de confirmation est incorrect.',
            ], 403);
        }

        Log::info("[$logId] Mot de passe validé. Préparation de l'appel Campay.");

        // 4. Lancement du retrait Campay
        try {
            $amount = $request->amount;
            $description = 'Retrait manuel par Admin: '.$admin->code_membre;

            Log::info("[$logId] [CAMPAY-PRE-FLIGHT] Envoi requête : Montant=$amount | Desc=$description");

            // Appel au service
            $response = $this->campayService->withdraw($amount, $description);

            // LOG COMPLET DE LA RÉPONSE POUR DEBUG
            Log::info("[$logId] [CAMPAY-RESPONSE-RAW] : ".json_encode($response));

            // Si Campay renvoie une référence (Succès)
            if (isset($response['reference'])) {
                Log::info("[$logId] ✅ Retrait réussi. Référence Campay : ".$response['reference']);

                return response()->json([
                    'success' => true,
                    'message' => "Le virement de {$amount} XAF a été envoyé vers votre mobile.",
                    'data' => $response,
                ]);
            }

            // Cas d'erreur 401 / 400 retourné par Campay (ton cas actuel)
            Log::error("[$logId] ❌ Campay a rejeté la requête.");
            Log::error("[$logId] Message d'erreur Campay : ".($response['message'] ?? 'Aucun message'));

            return response()->json([
                'success' => false,
                'message' => 'Campay a refusé la transaction : '.($response['message'] ?? 'Erreur inconnue'),
                'error_detail' => $response,
                'debug_id' => $logId, // On renvoie l'ID au front pour chercher dans les logs
            ], 400);

        } catch (\Exception $e) {
            // Erreurs PHP ou Crash de connexion
            Log::critical("[$logId] 💥 CRASH CRITIQUE : ".$e->getMessage());
            Log::critical($e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Exception technique : '.$e->getMessage(),
                'debug_id' => $logId,
            ], 500);
        }
    }
}
