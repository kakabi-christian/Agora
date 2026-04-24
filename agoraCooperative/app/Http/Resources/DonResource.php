<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DonResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'nom_donateur' => $this->anonyme ? 'Anonyme' : $this->nom_donateur,
            'email_donateur' => $this->when(!$this->anonyme, $this->email_donateur),
            'type' => $this->type,
            'montant' => $this->montant,
            'mode_paiement' => $this->mode_paiement,
            'reference_paiement' => $this->reference_paiement,
            'statut_paiement' => $this->statut_paiement,
            'date_don' => $this->date_don ? $this->date_don->format('Y-m-d') : null,
            'deductible_impots' => $this->deductible_impots,
            'numero_recu' => $this->numero_recu,
            'message_donateur' => $this->message_donateur,
            'anonyme' => $this->anonyme,
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
