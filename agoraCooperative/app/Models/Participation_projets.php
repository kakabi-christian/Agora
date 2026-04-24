<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Participation_projets extends Model
{
    use HasFactory;

    protected $table = 'participation_projets';

    protected $fillable = [
        'code_membre',
        'projet_id',
        'date_participation',
        'role',
        'statut',
        'heures_contribuees',
        'taches',
        'competences_apportees',
    ];

    protected $casts = [
        'date_participation' => 'date',
        'heures_contribuees' => 'integer',
    ];

    // Relations
    public function membre()
    {
        return $this->belongsTo(Membre::class, 'code_membre', 'code_membre');
    }

    public function projet()
    {
        return $this->belongsTo(Projets::class, 'projet_id', 'id');
    }
}
