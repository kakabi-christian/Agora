<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PasswordResetOtpRequest;
use App\Models\PasswordResetOtp;
use App\Models\Membre;
use App\Mail\ResetPasswordOtpMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log; // Importation pour les logs
use Carbon\Carbon;

class ForgotPasswordController extends Controller
{
    /**
     * ÉTAPE 1 : Générer et envoyer l'OTP
     */
    public function sendOtp(PasswordResetOtpRequest $request)
    {
        Log::info("=== DÉBUT RÉINITIALISATION MOT DE PASSE ===");
        Log::info("Email cible : " . $request->email);

        // 1. Générer un code aléatoire à 6 chiffres
        $otp = rand(100000, 999999);
        Log::info("OTP généré : " . $otp); // Utile pour tester sans ouvrir ses mails

        try {
            // 2. Enregistrer ou mettre à jour l'OTP pour cet email
            PasswordResetOtp::updateOrCreate(
                ['email' => $request->email],
                [
                    'otp' => Hash::make($otp),
                    'created_at' => now()
                ]
            );
            Log::info("OTP stocké en base de données (hashé).");

            // 3. Envoyer l'email
            Mail::to($request->email)->send(new ResetPasswordOtpMail($otp));
            Log::info("Email envoyé avec succès à " . $request->email);
            
            return response()->json([
                'success' => true,
                'message' => 'Le code de vérification a été envoyé à votre adresse email.'
            ], 200);

        } catch (\Exception $e) {
            Log::error("ERREUR lors de l'envoi de l'OTP : " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur technique lors de l\'envoi de l\'email.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * ÉTAPE 2 : Vérifier si l'OTP saisi est correct
     */
    public function verifyOtp(Request $request)
    {
        Log::info("=== VÉRIFICATION OTP ===");
        Log::info("Tentative pour : " . $request->email . " avec le code : " . $request->otp);

        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|digits:6'
        ]);

        $otpRecord = PasswordResetOtp::where('email', $request->email)->first();

        // 1. Vérification de l'existence
        if (!$otpRecord) {
            Log::warning("Vérification échouée : Aucun OTP trouvé pour " . $request->email);
            return response()->json(['success' => false, 'message' => 'Aucun code trouvé.'], 422);
        }

        // 2. Vérification de la correspondance (Hash)
        if (!Hash::check($request->otp, $otpRecord->otp)) {
            Log::warning("Vérification échouée : Code incorrect pour " . $request->email);
            return response()->json(['success' => false, 'message' => 'Code OTP invalide.'], 422);
        }

        // 3. Vérification de l'expiration
        if (Carbon::parse($otpRecord->created_at)->addMinutes(15)->isPast()) {
            Log::warning("Vérification échouée : Code expiré pour " . $request->email);
            return response()->json(['success' => false, 'message' => 'Ce code a expiré.'], 422);
        }

        Log::info("Vérification réussie pour " . $request->email);
        return response()->json([
            'success' => true,
            'message' => 'OTP valide. Vous pouvez maintenant changer votre mot de passe.'
        ], 200);
    }

    /**
     * ÉTAPE 3 : Mettre à jour le mot de passe
     */
    public function resetPassword(Request $request)
    {
        Log::info("=== MISE À JOUR MOT DE PASSE ===");
        Log::info("Email : " . $request->email);

        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $membre = Membre::where('email', $request->email)->first();

        if (!$membre) {
            Log::error("Échec Reset : Membre introuvable pour " . $request->email);
            return response()->json(['success' => false, 'message' => 'Utilisateur introuvable.'], 404);
        }

        // Mise à jour
        $membre->update([
            'mot_de_passe' => Hash::make($request->password)
        ]);
        Log::info("Mot de passe mis à jour en base de données pour le membre : " . $membre->code_membre);

        // Nettoyage
        PasswordResetOtp::where('email', $request->email)->delete();
        Log::info("OTP supprimé de la table temporaire.");
        Log::info("=== FIN PROCÉDURE RÉUSSIE ===");

        return response()->json([
            'success' => true,
            'message' => 'Votre mot de passe a été réinitialisé avec succès.'
        ], 200);
    }
}