<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MembreResource extends JsonResource
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
            'code_membre' => $this->code_membre,
            'nom' => $this->nom,
            'prenom' => $this->prenom,
            'email' => $this->email,
            'role' => $this->role,
            'est_actif' => $this->est_actif,
            'mot_de_passe_temporaire' => $this->mot_de_passe_temporaire,
            'telephone' => $this->telephone,
            'ville' => $this->ville,
            'photo_url' => $this->photo_url,
            'date_inscription' => $this->date_inscription? $this->date_inscription->format('Y-m-d'):null,
            'profil' => new ProfilResource($this->whenLoaded('profil')),
        ];
    }
}
