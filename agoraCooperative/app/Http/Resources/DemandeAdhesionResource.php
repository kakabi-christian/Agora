<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class DemandeAdhesionResource extends JsonResource
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
            'nom' => $this->nom,
            'prenom' => $this->prenom,
            'email' => $this->email,
            'telephone' => $this->telephone,
            
            // --- CHAMPS AJOUTÉS POUR L'AFFICHAGE SUR LES CARTES ---
            'profession' => $this->profession,
            'date_naissance' => $this->date_naissance,
            'ville' => $this->ville,
            'adresse' => $this->adresse,
            'code_postal' => $this->code_postal,
            // -----------------------------------------------------

            'motivation' => $this->motivation,
            'competences' => $this->competences,
            'statut' => $this->statut,
            
            // Formatage sécurisé des dates
            'date_demande' => $this->formatDate($this->date_demande),
            'date_traitement' => $this->formatDate($this->date_traitement),
            
            'admin_traitant' => new MembreResource($this->whenLoaded('adminTraitant')),
            'commentaire_admin' => $this->commentaire_admin,
            'membre_cree' => new MembreResource($this->whenLoaded('membreCree')),
        ];
    }

    /**
     * Helper pour formater les dates proprement
     */
    private function formatDate($date)
    {
        if (!$date) return null;
        return $date instanceof Carbon ? $date->format('Y-m-d H:i:s') : Carbon::parse($date)->format('Y-m-d H:i:s');
    }
}