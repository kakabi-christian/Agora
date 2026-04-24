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
            'fichier' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,zip|max:10240', // 10MB max
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
            'fichier.mimes' => 'Le fichier doit être au format: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, TXT ou ZIP.',
            'fichier.max' => 'Le fichier ne doit pas dépasser 10 MB.',
            'date_expiration.after' => 'La date d\'expiration doit être dans le futur.',
        ];
    }
}
