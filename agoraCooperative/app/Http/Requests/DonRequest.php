<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DonRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur est autorisé à faire cette requête.
     */
    public function authorize(): bool
    {
        return true; // Les dons sont ouverts au public
    }

    /**
     * Règles de validation.
     */
    public function rules(): array
    {
        return [
            'nom_donateur'      => 'required|string|max:255',
            'email_donateur'    => 'required|email|max:255',
            'telephone'         => 'required|string|min:9|max:20', // OBLIGATOIRE pour Campay
            'type'              => 'required|in:don,parrainage,adhesion,sponsoring',
            
            // On baisse le minimum à 5 pour permettre tes tests à 25 FCFA en démo
            'montant'           => 'required|numeric|min:5', 
            
            'message_donateur'  => 'nullable|string|max:1000',
            'anonyme'           => 'nullable|boolean',
            'code_membre'       => 'nullable|string|exists:membres,code_membre',
        ];
    }

    /**
     * Messages d'erreur personnalisés.
     */
    public function messages(): array
    {
        return [
            'nom_donateur.required'   => 'Le nom du donateur est requis.',
            'email_donateur.required' => 'L\'adresse email est requise.',
            'email_donateur.email'    => 'L\'adresse email doit être valide.',
            'telephone.required'      => 'Le numéro de téléphone est obligatoire pour le paiement mobile.',
            'type.required'           => 'Veuillez choisir le type de contribution.',
            'type.in'                 => 'Le type de contribution sélectionné est invalide.',
            'montant.required'        => 'Le montant du don est requis.',
            'montant.numeric'         => 'Le montant doit être un nombre.',
            'montant.min'             => 'Le montant minimum est de 5 FCFA (Mode Démo).',
            'code_membre.exists'      => 'Le code membre fourni n\'existe pas.',
        ];
    }
}