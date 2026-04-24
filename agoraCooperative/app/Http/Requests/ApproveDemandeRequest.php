<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApproveDemandeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // Permet à tous les utilisateurs autorisés (ex. admin) de faire la requête
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            // Le commentaire de l'admin est optionnel mais doit être une chaîne de max 500 caractères
            'commentaire_admin' => 'nullable|string|max:500',
        ];
    }

    /**
     * Get the custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages()
    {
        return [
            'commentaire_admin.max' => 'Le commentaire ne peut pas dépasser 500 caractères.',
            'commentaire_admin.string' => 'Le commentaire doit être une chaîne de caractères.',
        ];
    }
}
// app/Http/Requests/ApproveDemandeRequest.php