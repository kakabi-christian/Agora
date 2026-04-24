<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FaqRequest extends FormRequest
{
    /**
     * Autoriser la requête
     */
    public function authorize(): bool
    {
        return true; // On autorise la requête pour l’instant
    }

    /**
     * Règles de validation
     */
    public function rules(): array
    {
        return [
            'question' => 'required|string|max:1000',
            'reponse' => 'required|string|max:3000',
            'categorie' => 'required|in:generale,membres,projets,dons,evenements,administratif',
            'ordre_affichage' => 'nullable|integer|min:0',
            'est_actif' => 'nullable|boolean',
        ];
    }

    /**
     * Messages d’erreur personnalisés
     */
    public function messages(): array
    {
        return [
            'question.required' => 'La question est obligatoire.',
            'question.string' => 'La question doit être une chaîne de caractères.',
            'reponse.required' => 'La réponse est obligatoire.',
            'reponse.string' => 'La réponse doit être une chaîne de caractères.',
            'categorie.required' => 'La catégorie est obligatoire.',
            'categorie.in' => 'La catégorie est invalide.',
            'ordre_affichage.integer' => 'L’ordre d’affichage doit être un nombre entier.',
            'ordre_affichage.min' => 'L’ordre d’affichage ne peut pas être négatif.',
            'est_actif.boolean' => 'Le champ est_actif doit être vrai ou faux.',
        ];
    }
}
