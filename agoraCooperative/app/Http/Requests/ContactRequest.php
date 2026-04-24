<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ContactRequest extends FormRequest
{
    /**
     * On autorise tout le monde à envoyer un message de contact (public).
     */
    public function authorize()
    {
        return true; 
    }

    /**
     * Règles de validation
     */
    public function rules()
    {
        return [
            'nom_expediteur'   => 'required|string|max:255',
            'email_expediteur'  => 'required|email|max:255',
            'sujet'            => 'required|string|max:255',
            'message'          => 'required|string',
            'type_demande'     => 'required|in:information,support,partenariat,autre',
            'telephone'        => 'nullable|string|max:20',
            'code_membre'      => 'nullable|exists:membres,code_membre',
        ];
    }

    /**
     * Personnalisation des messages d'erreur (Optionnel)
     */
    public function messages()
    {
        return [
            'type_demande.in' => 'Le type de demande doit être : information, support, partenariat ou autre.',
            'email_expediteur.email' => 'Veuillez fournir une adresse email valide.',
        ];
    }

    /**
     * Formatage de la réponse en cas d'erreur de validation pour l'API
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'errors'  => $validator->errors()
        ], 422));
    }
}
