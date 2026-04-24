<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DemandeAdhesionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|unique:demandes_adhesion,email',
            'telephone' => 'required|string|regex:/^[0-9]{9}$/',
            'adresse' => 'nullable|string',
            'ville' => 'nullable|string',
            'code_postal' => 'nullable|string|regex:/^[0-9]{5}$/',
            'date_naissance' => 'nullable|date|before:today',
            'profession' => 'nullable|string',
            'motivation' => 'required|string|min:50|max:1000',
            'competences' => 'nullable|array',
        ];
    }

    public function messages()
    {
        return [
            'nom.required' => 'Le nom est obligatoire.',
            'prenom.required' => 'Le prénom est obligatoire.',
            'email.required' => 'L\'adresse email est obligatoire.',
            'email.email' => 'L\'adresse email doit être valide.',
            'email.unique' => 'Une demande avec cet email existe déjà.',
            'telephone.required' => 'Le numéro de téléphone est obligatoire.',
            'telephone.regex' => 'Le numéro de téléphone doit contenir 9 chiffres.',
            'code_postal.regex' => 'Le code postal doit contenir 5 chiffres.',
            'date_naissance.before' => 'La date de naissance doit être antérieure à aujourd\'hui.',
            'motivation.required' => 'La motivation est obligatoire.',
            'motivation.min' => 'La motivation doit contenir au moins 50 caractères.',
            'motivation.max' => 'La motivation ne peut pas dépasser 1000 caractères.',
        ];
    }
}
