<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Membre extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $table = 'membres';
    protected $primaryKey = 'code_membre';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'code_membre',
        'nom',
        'prenom',
        'email',
        'mot_de_passe',
        'mot_de_passe_temporaire',
        'date_inscription',
        'role',
        'est_actif',
        'telephone',
        'adresse',
        'ville',
        'code_postal',
        'biographie',
        'photo_url',
    ];

    protected $hidden = [
        'mot_de_passe',
        'remember_token',
    ];

    protected $casts = [
        'date_inscription' => 'date',
        'est_actif' => 'boolean',
        'mot_de_passe_temporaire' => 'boolean',
    ];

    // Relations
    public function profil()
    {
        return $this->hasOne(Profils::class, 'code_membre', 'code_membre');
    }

    public function inscriptionEvents()
    {
        return $this->hasMany(Inscription_events::class, 'code_membre', 'code_membre');
    }

    public function participationProjets()
    {
        return $this->hasMany(Participation_projets::class, 'code_membre', 'code_membre');
    }

    public function dons()
    {
        return $this->hasMany(Don::class, 'code_membre', 'code_membre');
    }

    public function ressources()
    {
        return $this->hasMany(Ressources::class, 'code_membre', 'code_membre');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'code_membre', 'code_membre');
    }

    public function historiqueParticipations()
    {
        return $this->hasMany(HistoriqueParticipation::class, 'code_membre', 'code_membre');
    }

    public function paiements()
    {
        return $this->hasMany(Paiement::class, 'code_membre', 'code_membre');
    }
    /**
     * Override pour indiquer à Laravel quel champ utiliser comme mot de passe
     */
    public function getAuthPassword()
    {
        return $this->mot_de_passe;
    }

    /**
     * Accessor pour vérifier si le membre est admin
     */
    public function getEstAdminAttribute()
    {
        return $this->role === 'administrateur';
    }
}
