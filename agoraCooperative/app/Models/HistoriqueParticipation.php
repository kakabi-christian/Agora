<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoriqueParticipation extends Model
{
    use HasFactory;

    protected $table = 'historique_participations';

    protected $fillable = [
        'code_membre',
        'type_participation',
        'titre',
        'description',
        'date_participation',
        'details',
        'montant_implique',
        'heures_contribuees',
        'role',
    ];

    protected $casts = [
        'date_participation' => 'date',
        'details' => 'array',
        'montant_implique' => 'decimal:2',
        'heures_contribuees' => 'integer',
    ];

    // Relations
    public function membre()
    {
        return $this->belongsTo(Membre::class, 'code_membre', 'code_membre');
    }
}
