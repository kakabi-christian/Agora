<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ParticipationProjetResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'code_membre' => $this->code_membre,
            'projet_id' => $this->projet_id,
            'date_participation' => $this->date_participation ? $this->date_participation->format('Y-m-d') : null,
            'role' => $this->role,
            'statut' => $this->statut,
            'heures_contribuees' => $this->heures_contribuees,
            'taches' => $this->taches,
            'competences_apportees' => $this->competences_apportees,
            'projet' => $this->whenLoaded('projet', function () {
                return [
                    'id' => $this->projet->id,
                    'nom' => $this->projet->nom,
                    'statut' => $this->projet->statut,
                    'date_debut' => $this->projet->date_debut ? $this->projet->date_debut->format('Y-m-d') : null,
                    'date_fin_prevue' => $this->projet->date_fin_prevue ? $this->projet->date_fin_prevue->format('Y-m-d') : null,
                ];
            }),
            'membre' => $this->whenLoaded('membre', function () {
                return [
                    'code_membre' => $this->membre->code_membre,
                    'nom' => $this->membre->nom,
                    'prenom' => $this->membre->prenom,
                ];
            }),
            'created_at' => $this->created_at ? $this->created_at->format('Y-m-d H:i:s') : null,
        ];
    }
}
