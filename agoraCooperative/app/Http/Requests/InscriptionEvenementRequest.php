<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InscriptionEvenementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'commentaires' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'commentaires.max' => 'Les commentaires ne peuvent pas dépasser 500 caractères.',
        ];
    }
}
