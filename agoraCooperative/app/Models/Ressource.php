<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Modèle Ressource
 * Gère les documents et fichiers partagés de la plateforme.
 */
class Ressource extends Model
{
    use HasFactory, SoftDeletes;

    // On définit explicitement la table car elle est au pluriel
    protected $table = 'ressources';

    protected $fillable = [
        'titre',
        'type',
        'categorie',
        'chemin_fichier',
        'nom_fichier',
        'extension_fichier',
        'description',
        'date_publication',
        'date_expiration',
        'est_public',
        'necessite_authentification',
        'nombre_telechargements',
        'code_membre',
    ];

    protected $casts = [
        'date_publication' => 'date',
        'date_expiration' => 'date',
        'est_public' => 'boolean',
        'necessite_authentification' => 'boolean',
        'nombre_telechargements' => 'integer',
    ];

    /**
     * Relation : La ressource appartient à un membre (l'uploader).
     */
    public function uploader()
    {
        // Utilisation de code_membre comme clé étrangère et clé locale
        return $this->belongsTo(Membre::class, 'code_membre', 'code_membre');
    }
    
    /**
     * Alias pour la relation uploader.
     */
    public function membre()
    {
        return $this->belongsTo(Membre::class, 'code_membre', 'code_membre');
    }
}