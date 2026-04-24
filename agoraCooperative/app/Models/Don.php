<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Don extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'dons';

    protected $fillable = [
        'code_membre',
        'nom_donateur',
        'email_donateur',
        'telephone',           
        'type',
        'montant', // Sera chiffré
        'mode_paiement',       
        'reference_paiement',  
        'statut_paiement',     
        'date_don',
        'deductible_impots',
        'numero_recu',
        'message_donateur',
        'anonyme',
        'informations_donateur',
    ];

    /**
     * Seul le montant est chiffré en base de données.
     */
    protected $casts = [
        'montant' => 'encrypted', // Chiffre en BD, déchiffre à la lecture
        
        'date_don' => 'date',
        'deductible_impots' => 'boolean',
        'anonyme' => 'boolean',
        'informations_donateur' => 'array',
    ];

    // --- Relations ---
    
    public function membre()
    {
        return $this->belongsTo(Membre::class, 'code_membre', 'code_membre');
    }

    // --- Scopes & Helpers ---

    public function scopeConfirmes($query)
    {
        return $query->where('statut_paiement', 'succes');
    }

    public function estValide(): bool
    {
        return $this->statut_paiement === 'succes';
    }
}