<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Faq extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'faqs';

    protected $fillable = [
        'question',
        'reponse',
        'categorie',
        'ordre_affichage',
        'est_actif',
        'nombre_vues',
        'nombre_utile',
        'nombre_inutile',
    ];

    protected $casts = [
        'est_actif' => 'boolean',
        'ordre_affichage' => 'integer',
        'nombre_vues' => 'integer',
        'nombre_utile' => 'integer',
        'nombre_inutile' => 'integer',
    ];
}
