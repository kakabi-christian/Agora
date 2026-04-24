<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Partenaire extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'partenaires';
    protected $primaryKey = 'code_partenaire';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'code_partenaire',
        'nom',
        'type',
        'description',
        'logo_url',
        'site_web',
        'contact_nom',
        'contact_email',
        'contact_telephone',
        'niveau_partenariat',
        'date_debut',
        'date_fin',
        'est_actif',
        'ordre_affichage',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
        'est_actif' => 'boolean',
        'ordre_affichage' => 'integer',
    ];
}
