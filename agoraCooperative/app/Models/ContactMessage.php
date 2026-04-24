<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    use HasFactory;

    protected $table = 'contact_messages';

    protected $fillable = [
        'code_membre',
        'nom_expediteur',
        'email_expediteur',
        'telephone',
        'sujet',
        'message',
        'type_demande',
        'statut',
        'code_admin_assignee',
        'reponse',
        'date_reponse',
        'lu',
        'date_lu',
    ];

    protected $casts = [
        'date_reponse' => 'datetime',
        'date_lu' => 'datetime',
        'lu' => 'boolean',
    ];

    // Relations
    public function membre()
    {
        return $this->belongsTo(Membre::class, 'code_membre', 'code_membre');
    }

    public function adminAssignee()
    {
        return $this->belongsTo(Membre::class, 'code_admin_assignee', 'code_membre');
    }
}
