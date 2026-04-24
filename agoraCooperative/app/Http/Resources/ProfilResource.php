<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfilResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'informations_personnelles' => $this->informations_personnelles,
            'competences' => $this->competences,
            'interets' => $this->interets,
            'date_derniere_connexion' => $this->date_derniere_connexion ? $this->date_derniere_connexion->format('Y-m-d H:i:s') : null,
            'nombre_participations' => $this->nombre_participations,
            'preferences' => $this->preferences,
        ];
    }
}
