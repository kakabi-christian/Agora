<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EvenementRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // Mettre à true pour autoriser la création/modification d'événements
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */public function rules()
{
    return [
        // Changé de 'required' à 'nullable' car généré en interne
        'code_evenement' => 'nullable|string|unique:evenements,code_evenement',
        'titre' => 'required|string|max:255',
        'description' => 'required|string',
        'date_debut' => 'required|date',
        'date_fin' => 'nullable|date|after_or_equal:date_debut',
        'lieu' => 'required|string|max:255',
        'adresse' => 'nullable|string|max:255',
        'ville' => 'nullable|string|max:100',
        'frais_inscription' => 'nullable|numeric|min:0',
        'places_disponibles' => 'nullable|integer|min:0',
        'type' => 'required|in:assemblee,atelier,reunion,formation,autre',
        'statut' => 'nullable|in:planifie,en_cours,termine,annule',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'instructions' => 'nullable|string',
        'paiement_obligatoire' => 'nullable', // Changé pour accepter 0/1 ou true/false
    ];
}
}
// app/Http/Requests/EvenementRequest.php