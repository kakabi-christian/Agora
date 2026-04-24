<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Evenements extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'evenements';
    protected $primaryKey = 'code_evenement';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'code_evenement',
        'titre',
        'description',
        'date_debut',
        'date_fin',
        'lieu',
        'adresse',
        'ville',
        'frais_inscription',
        'places_disponibles',
        'type',
        'statut',
        'image_url',
        'instructions',
        'paiement_obligatoire',
    ];

    protected $casts = [
        'date_debut' => 'datetime',
        'date_fin' => 'datetime',
        'frais_inscription' => 'decimal:2',
        'places_disponibles' => 'integer',
        'paiement_obligatoire' => 'boolean',
    ];

    // Relations
    public function inscriptions()
    {
        return $this->hasMany(Inscription_events::class, 'code_evenement', 'code_evenement');
    }
}
