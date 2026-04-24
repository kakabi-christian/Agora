<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Resources\AuthResource;
use App\Http\Resources\MembreResource;
use App\Models\Membre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;

class AuthController extends Controller
{
    /**
     * Authenticate a member and return token.
     *
     * @param  \App\Http\Requests\LoginRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function login(LoginRequest $request)
    {
        $key = 'login:' . $request->ip();
        
        // Vérifier rate limiting (5 tentatives / 15 min)
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            
            return response()->json([
                'message' => "Trop de tentatives de connexion. Veuillez réessayer dans {$seconds} secondes."
            ], 429);
        }
        
        // Trouver le membre par email
        $membre = Membre::where('email', $request->email)->first();
        
        // Vérifier les identifiants
        if (!$membre || !Hash::check($request->mot_de_passe, $membre->mot_de_passe)) {
            RateLimiter::hit($key, 60); // 15 minutes
            
            return response()->json([
                'message' => 'Identifiants incorrects.'
            ], 401);
        }
        
        // Vérifier que le compte est actif
        if (!$membre->est_actif) {
            return response()->json([
                'message' => 'Votre compte est désactivé. Veuillez contacter un administrateur.'
            ], 403);
        }
        
        // Réinitialiser le rate limiter en cas de succès
        RateLimiter::clear($key);
        
        // Créer le token Sanctum
        $token = $membre->createToken('auth-token')->plainTextToken;
        
        // Mettre à jour la date de dernière connexion
        if ($membre->profil) {
            $membre->profil->update([
                'date_derniere_connexion' => now(),
            ]);
        }
        
        return new AuthResource([
            'membre' => $membre->load('profil'),
            'token' => $token,
        ]);
    }

    /**
     * Logout the authenticated member.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        // Révoquer le token actuel
        $request->user()->currentAccessToken()->delete();
        
        return response()->json(null, 204);
    }

    /**
     * Refresh the authentication token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function refresh(Request $request)
    {
        $membre = $request->user();
        
        // Révoquer l'ancien token
        $request->user()->currentAccessToken()->delete();
        
        // Créer un nouveau token
        $token = $membre->createToken('auth-token')->plainTextToken;
        
        return new AuthResource([
            'membre' => $membre->load('profil'),
            'token' => $token,
        ]);
    }

    /**
     * Get the authenticated member profile.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function me(Request $request)
    {
        $membre = $request->user()->load('profil');
        
        return new MembreResource($membre);
    }

    /**
     * Change the authenticated member's password.
     *
     * @param  \App\Http\Requests\ChangePasswordRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function changePassword(ChangePasswordRequest $request)
    {
        $membre = $request->user();

        // Vérifier l'ancien mot de passe
        if (!Hash::check($request->ancien_mot_de_passe, $membre->mot_de_passe)) {
            return response()->json([
                'success' => false,
                'message' => 'L\'ancien mot de passe est incorrect.'
            ], 400);
        }

        // Vérifier que le nouveau mot de passe est différent
        if (Hash::check($request->nouveau_mot_de_passe, $membre->mot_de_passe)) {
            return response()->json([
                'success' => false,
                'message' => 'Le nouveau mot de passe doit être différent de l\'ancien.'
            ], 400);
        }

        // Mettre à jour le mot de passe
        $membre->update([
            'mot_de_passe' => Hash::make($request->nouveau_mot_de_passe),
            'mot_de_passe_temporaire' => false, // Plus temporaire
        ]);

        \Log::info('Changement de mot de passe', [
            'code_membre' => $membre->code_membre,
            'email' => $membre->email,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Mot de passe changé avec succès.'
        ]);
    }
}
