<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PasswordResetOtpRequest extends FormRequest
{
    /**
     * On autorise tout le monde à faire cette requête (puisqu'ils sont déconnectés)
     */
    public function authorize()
    {
        return true; 
    }

    /**
     * Les règles de validation pour l'envoi de l'OTP
     */
    public function rules()
    {
        return [
            'email' => 'required|email|exists:membres,email',
        ];
    }

    /**
     * Messages d'erreur personnalisés
     */
    public function messages()
    {
        return [
            'email.required' => 'L\'adresse email est obligatoire.',
            'email.email'    => 'Veuillez entrer une adresse email valide.',
            'email.exists'   => 'Aucun compte n\'est associé à cet email.',
        ];
    }
}