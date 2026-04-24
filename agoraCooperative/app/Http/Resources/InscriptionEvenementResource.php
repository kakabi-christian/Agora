<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class InscriptionEvenementResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'code_membre' => $this->code_membre,
            'code_evenement' => $this->code_evenement,
            'date_inscription' => $this->date_inscription ? $this->date_inscription->format('Y-m-d H:i:s') : null,
            'statut_paiement' => $this->statut_paiement,
            'statut_participation' => $this->statut_participation,
            'montant_paye' => $this->montant_paye,
            'mode_paiement' => $this->mode_paiement,
            'reference_paiement' => $this->reference_paiement,
            'commentaires' => $this->commentaires,
            'evenement' => $this->whenLoaded('evenement', function () {
                return [
                    'code_evenement' => $this->evenement->code_evenement,
                    'titre' => $this->evenement->titre,
                    'date_debut' => $this->evenement->date_debut ? $this->evenement->date_debut->format('Y-m-d H:i:s') : null,
                    'lieu' => $this->evenement->lieu,
                    'frais_inscription' => $this->evenement->frais_inscription,
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
