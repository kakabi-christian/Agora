<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProjetRequest extends FormRequest
{
    /**
     * Autoriser la requête
     */
    public function authorize(): bool
    {
        // Mettre à true pour permettre l'utilisation de cette request
        return true;
    }

    /**
     * Règles de validation
     */
    public function rules(): array
    {
        return [
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:agricole,social,environnemental,educatif,autre',
            'statut' => 'nullable|in:propose,en_etude,approuve,en_cours,termine,annule',
            'date_debut' => 'nullable|date',
            'date_fin_prevue' => 'nullable|date|after_or_equal:date_debut',
            'date_fin_reelle' => 'nullable|date|after_or_equal:date_debut',
            'budget_estime' => 'nullable|numeric|min:0',
            'budget_reel' => 'nullable|numeric|min:0',
            'coordinateur' => 'nullable|string|max:255',
            'objectifs' => 'nullable|array',
            'objectifs.*' => 'string|max:255',
            'resultats' => 'nullable|array',
            'resultats.*' => 'string|max:255',
            'image_url' => 'nullable|image|mimes:jpg,jpeg,png,svg|max:3072', // 3 Mo max
            'notes' => 'nullable|string',
            'est_public' => 'nullable|boolean',
        ];
    }

    /**
     * Messages d’erreur personnalisés
     */
    public function messages(): array
    {
        return [
            'nom.required' => 'Le nom du projet est obligatoire.',
            'type.in' => 'Le type du projet est invalide.',
            'statut.in' => 'Le statut du projet est invalide.',
            'date_fin_prevue.after_or_equal' => 'La date de fin prévue doit être après la date de début.',
            'date_fin_reelle.after_or_equal' => 'La date de fin réelle doit être après la date de début.',
            'budget_estime.numeric' => 'Le budget estimé doit être un nombre.',
            'budget_reel.numeric' => 'Le budget réel doit être un nombre.',
            'image_url.image' => 'Le fichier doit être une image valide.',
            'image_url.mimes' => 'Le fichier doit être au format jpg, jpeg, png ou svg.',
            'image_url.max' => 'L’image ne doit pas dépasser 3 Mo.',
        ];
    }
}
