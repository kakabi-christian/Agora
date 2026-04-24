<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PartenaireRequest extends FormRequest
{
    /**
     * Autoriser la requête
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Règles de validation
     */
    public function rules(): array
    {
        return [
            'nom' => 'required|string|max:255',

            'type' => 'required|in:partenaire,sponsor,institution,association',

            'description' => 'nullable|string',

            'logo' => 'nullable|image|mimes:png,jpg,jpeg,svg|max:3072', // 3072 Ko = 3 Mo

            'site_web' => 'nullable|url|max:255',

            'contact_nom' => 'nullable|string|max:255',

            'contact_email' => 'nullable|email|max:255',

            'contact_telephone' => 'nullable|string|max:30',

            'niveau_partenariat' => 'nullable|in:principal,secondaire,tertiaire',

            'date_debut' => 'nullable|date',

            'date_fin' => 'nullable|date|after_or_equal:date_debut',

            'est_actif' => 'nullable|boolean',

            'ordre_affichage' => 'nullable|integer|min:0',
        ];
    }

    /**
     * Messages d’erreur personnalisés (optionnel mais pro)
     */
    public function messages(): array
    {
        return [
            'nom.required' => 'Le nom du partenaire est obligatoire.',
            'type.in' => 'Le type de partenaire est invalide.',
            'logo.image' => 'Le logo doit être une image valide.',
            'contact_email.email' => 'Email du contact invalide.',
            'date_fin.after_or_equal' => 'La date de fin doit être après la date de début.',
        ];
    }
}
