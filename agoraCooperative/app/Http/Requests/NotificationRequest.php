<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class NotificationRequest extends FormRequest
{
    /**
     * On autorise la requête.
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
            // Détermine si on envoie à tout le monde
            'pour_tous'       => 'required|boolean',
            
            // Requis uniquement si 'pour_tous' est false
            'code_membre'     => 'required_if:pour_tous,false|nullable|exists:membres,code_membre',
            
            'titre'           => 'required|string|max:255',
            'contenu'         => 'required|string',
            'type'            => 'required|in:email,alerte_site,notification_mobile,sms',
            'categorie'       => 'required|in:systeme,evenement,projet,administratif,urgence',
            'est_urgent'      => 'boolean',
            'lien_action'     => 'nullable|string',
            
            // Pour le polymorphisme si tu l'utilises déjà
            'objet_relie_type' => 'nullable|string|max:50',
            'objet_relie_code' => 'nullable|string|max:50',
        ];
    }

    /**
     * Messages d'erreur personnalisés
     */
    public function messages()
    {
        return [
            'code_membre.required_if' => 'Le membre est obligatoire si vous n\'envoyez pas à tout le monde.',
            'code_membre.exists'      => 'Ce code membre n\'existe pas dans notre base.',
            'type.in'                 => 'Le type doit être : email, alerte_site, notification_mobile ou sms.',
        ];
    }

    /**
     * Formatage de la réponse JSON en cas d'échec
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'errors'  => $validator->errors()
        ], 422));
    }
}