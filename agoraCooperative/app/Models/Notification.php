<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $table = 'notifications';

    protected $fillable = [
        'code_membre',
        'titre',
        'contenu',
        'type',
        'categorie',
        'statut',
        'objet_relie_type',
        'objet_relie_code',
        'date_envoi',
        'date_lecture',
        'lien_action',
        'est_urgent',
    ];

    protected $casts = [
        'date_envoi' => 'datetime',
        'date_lecture' => 'datetime',
        'est_urgent' => 'boolean',
    ];

    // Relations
    public function membre()
    {
        return $this->belongsTo(Membre::class, 'code_membre', 'code_membre');
    }

    // Relation polymorphique
    public function objetRelie()
    {
        return $this->morphTo('objet_relie', 'objet_relie_type', 'objet_relie_code');
    }
}
