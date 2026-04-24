<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class HistoriqueParticipationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'type_participation' => $this->type_participation,
            'titre' => $this->titre,
            'description' => $this->description,
            'date_participation' => $this->date_participation? $this->date_participation->format('Y-m-d'): null,
            'details' => $this->details,
            'montant_implique' => $this->montant_implique ? number_format($this->montant_implique, 2) : null,
            'heures_contribuees' => $this->heures_contribuees,
            'role' => $this->role,
        ];
    }
}
