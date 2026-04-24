<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'ancien_mot_de_passe' => 'required|string',
            'nouveau_mot_de_passe' => 'required|string|min:8|confirmed',
            'nouveau_mot_de_passe_confirmation' => 'required|string',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'ancien_mot_de_passe.required' => 'L\'ancien mot de passe est requis.',
            'nouveau_mot_de_passe.required' => 'Le nouveau mot de passe est requis.',
            'nouveau_mot_de_passe.min' => 'Le nouveau mot de passe doit contenir au moins 8 caractères.',
            'nouveau_mot_de_passe.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
            'nouveau_mot_de_passe_confirmation.required' => 'La confirmation du mot de passe est requise.',
        ];
    }
}
