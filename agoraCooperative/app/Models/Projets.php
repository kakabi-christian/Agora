<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Projets extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'projets';

    protected $fillable = [
        'nom',
        'description',
        'type',
        'statut',
        'date_debut',
        'date_fin_prevue',
        'date_fin_reelle',
        'budget_estime',
        'budget_reel',
        'coordinateur',
        'objectifs',
        'resultats',
        'image_url',
        'notes',
        'est_public',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin_prevue' => 'date',
        'date_fin_reelle' => 'date',
        'budget_estime' => 'decimal:2',
        'budget_reel' => 'decimal:2',
        'objectifs' => 'array',
        'resultats' => 'array',
        'est_public' => 'boolean',
    ];

    // Relations
    public function participations()
    {
        return $this->hasMany(Participation_projets::class, 'projet_id', 'id');
    }
}
