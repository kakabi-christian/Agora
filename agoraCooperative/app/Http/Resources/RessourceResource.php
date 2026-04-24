<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RessourceResource extends JsonResource
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
            'titre' => $this->titre,
            'type' => $this->type,
            'categorie' => $this->categorie,
            'nom_fichier' => $this->nom_fichier,
            'extension_fichier' => $this->extension_fichier,
            'description' => $this->description,
            'date_publication' => $this->date_publication? $this->date_publication->format('Y-m-d'): null,
            'date_expiration' => $this->date_expiration? $this->date_expiration->format('Y-m-d'): null,
            'est_public' => $this->est_public,
            'nombre_telechargements' => $this->nombre_telechargements,
            'uploader' => new MembreResource($this->whenLoaded('uploader')),
            'download_url' => route('api.ressources.download', $this->id),
        ];
    }
}
