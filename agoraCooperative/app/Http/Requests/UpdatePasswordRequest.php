<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePasswordRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'ancien_mot_de_passe' => 'required|string',
            'nouveau_mot_de_passe' => 'required|string|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/|confirmed',
            'nouveau_mot_de_passe_confirmation' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'ancien_mot_de_passe.required' => 'L\'ancien mot de passe est obligatoire.',
            'nouveau_mot_de_passe.required' => 'Le nouveau mot de passe est obligatoire.',
            'nouveau_mot_de_passe.min' => 'Le nouveau mot de passe doit contenir au moins 8 caractères.',
            'nouveau_mot_de_passe.regex' => 'Le nouveau mot de passe doit contenir au moins une majuscule, une minuscule et un chiffre.',
            'nouveau_mot_de_passe.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
            'nouveau_mot_de_passe_confirmation.required' => 'La confirmation du mot de passe est obligatoire.',
        ];
    }
}
