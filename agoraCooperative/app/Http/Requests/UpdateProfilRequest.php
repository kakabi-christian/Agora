<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfilRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'informations_personnelles' => 'sometimes|array',
            'competences' => 'sometimes|array',
            'interets' => 'sometimes|array',
            'preferences' => 'sometimes|array',
        ];
    }
}
