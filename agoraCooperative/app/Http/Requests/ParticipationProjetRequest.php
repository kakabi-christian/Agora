<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ParticipationProjetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'role' => 'nullable|string|max:100',
            'taches' => 'nullable|string|max:500',
            'competences_apportees' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'role.max' => 'Le rôle ne peut pas dépasser 100 caractères.',
            'taches.max' => 'Les tâches ne peuvent pas dépasser 500 caractères.',
        ];
    }
}
