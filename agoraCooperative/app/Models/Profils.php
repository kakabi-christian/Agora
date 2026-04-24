<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profils extends Model
{
    use HasFactory;

    protected $table = 'profils';

    protected $fillable = [
        'code_membre',
        'informations_personnelles',
        'competences',
        'interets',
        'date_derniere_connexion',
        'nombre_participations',
        'preferences',
    ];

    protected $casts = [
        'informations_personnelles' => 'array',
        'competences' => 'array',
        'interets' => 'array',
        'preferences' => 'array',
        'date_derniere_connexion' => 'date',
        'nombre_participations' => 'integer',
    ];

    // Relations
    public function membre()
    {
        return $this->belongsTo(Membre::class, 'code_membre', 'code_membre');
    }
}
// app/Models/Profils.php