<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RejectDemandeRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'commentaire_admin' => 'required|string|min:20|max:500',
        ];
    }

    public function messages()
    {
        return [
            'commentaire_admin.required' => 'Le commentaire est obligatoire pour rejeter une demande.',
            'commentaire_admin.min' => 'Le commentaire doit contenir au moins 20 caractères.',
            'commentaire_admin.max' => 'Le commentaire ne peut pas dépasser 500 caractères.',
        ];
    }
}
