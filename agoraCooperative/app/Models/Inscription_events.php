<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inscription_events extends Model
{
    use HasFactory;

    protected $table = 'inscription_events';

    protected $fillable = [
        'code_membre',
        'code_evenement',
        'date_inscription',
        'statut_paiement',
        'statut_participation',
        'montant_paye',
        'mode_paiement',
        'reference_paiement',
        'commentaires',
        'rappel_envoye',
        'date_rappel_envoye',
    ];

    protected $casts = [
        'date_inscription' => 'datetime',
        'montant_paye' => 'decimal:2',
        'rappel_envoye' => 'boolean',
        'date_rappel_envoye' => 'datetime',
    ];

    // Relations
    public function membre()
    {
        return $this->belongsTo(Membre::class, 'code_membre', 'code_membre');
    }

    public function evenement()
    {
        return $this->belongsTo(Evenements::class, 'code_evenement', 'code_evenement');
    }
}
