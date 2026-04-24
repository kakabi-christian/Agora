<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paiement extends Model
{
    use HasFactory;

    protected $table = 'paiements';
    protected $primaryKey = 'code_paiement';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'code_paiement',
        'code_membre',
        'reference',
        'montant',
        'type',
        'statut',
        'mode_paiement',
        'details_paiement',
        'transaction_id',
        'objet_relie_type',
        'objet_relie_code',
        'date_paiement',
    ];

    protected $casts = [
        'montant' => 'decimal:2',
        'details_paiement' => 'array',
        'date_paiement' => 'datetime',
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
