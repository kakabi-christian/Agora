<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMembreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'nom' => 'sometimes|string|max:255',
            'prenom' => 'sometimes|string|max:255',
            'telephone' => 'sometimes|string|regex:/^[0-9]{10}$/',
            'adresse' => 'sometimes|string',
            'ville' => 'sometimes|string',
            'code_postal' => 'sometimes|string|regex:/^[0-9]{5}$/',
            'biographie' => 'sometimes|string|max:1000',
        ];
    }

    public function messages()
    {
        return [
            'telephone.regex' => 'Le numéro de téléphone doit contenir 10 chiffres.',
            'code_postal.regex' => 'Le code postal doit contenir 5 chiffres.',
            'biographie.max' => 'La biographie ne peut pas dépasser 1000 caractères.',
        ];
    }
}
