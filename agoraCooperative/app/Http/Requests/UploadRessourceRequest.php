<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadRessourceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // Seuls les admins peuvent uploader des ressources
        return auth()->check() && auth()->user()->est_admin;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'titre' => 'required|string|max:255',
            'type' => 'required|in:document,formulaire,rapport,reglement,autre',
            'categorie' => 'required|in:administratif,comptable,juridique,technique,pedagogique',
            /* MISE À JOUR SÉCURITÉ : 
               1. Retrait du format .zip (risque de Zip Bomb / Malware).
               2. Ajout de 'mimetypes' pour une vérification plus stricte du contenu réel du fichier.
            */
            'fichier' => [
                'required',
                'file',
                'mimes:pdf,doc,docx,xls,xlsx,txt', 
                'mimetypes:application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,text/plain',
                'max:5120', // Réduction à 5MB pour limiter les risques de DoS par upload massif
            ],
            'description' => 'nullable|string|max:1000',
            'date_expiration' => 'nullable|date|after:today',
            'est_public' => 'nullable|boolean',
            'necessite_authentification' => 'nullable|boolean',
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
            'titre.required' => 'Le titre est obligatoire.',
            'type.required' => 'Le type de ressource est obligatoire.',
            'type.in' => 'Le type doit être: document, formulaire, rapport, règlement ou autre.',
            'categorie.required' => 'La catégorie est obligatoire.',
            'categorie.in' => 'La catégorie doit être: administratif, comptable, juridique, technique ou pédagogique.',
            'fichier.required' => 'Le fichier est obligatoire.',
            'fichier.mimes' => 'Format non supporté. Utilisez PDF, Office ou TXT.',
            'fichier.mimetypes' => 'Le contenu du fichier ne correspond pas à son extension.',
            'fichier.max' => 'Le fichier est trop volumineux (Maximum 5 MB).',
            'date_expiration.after' => 'La date d\'expiration doit être dans le futur.',
        ];
    }
}