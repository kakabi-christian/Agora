<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DemandeAdhesion extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'demandes_adhesion';

    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'telephone',
        'adresse',
        'ville',
        'code_postal',
        'date_naissance',
        'profession',
        'motivation',
        'competences',
        'statut',
        'date_demande',
        'date_traitement',
        'code_admin_traitant',
        'commentaire_admin',
        'documents_joints',
        'code_membre_cree',
    ];

    protected $casts = [
        'date_naissance' => 'date',
        'date_demande' => 'datetime',
        'date_traitement' => 'datetime',
        'competences' => 'array',
        'documents_joints' => 'array',
    ];

    // Relations
    public function adminTraitant()
    {
        return $this->belongsTo(Membre::class, 'code_admin_traitant', 'code_membre');
    }

    public function membreCree()
    {
        return $this->belongsTo(Membre::class, 'code_membre_cree', 'code_membre');
    }

    // Méthodes helper
    public function approuver($codeAdmin, $commentaire = null)
    {
        $this->update([
            'statut' => 'approuvee',
            'date_traitement' => now(),
            'code_admin_traitant' => $codeAdmin,
            'commentaire_admin' => $commentaire,
        ]);
    }

    public function rejeter($codeAdmin, $commentaire)
    {
        $this->update([
            'statut' => 'rejetee',
            'date_traitement' => now(),
            'code_admin_traitant' => $codeAdmin,
            'commentaire_admin' => $commentaire,
        ]);
    }

    public function mettreEnExamen($codeAdmin)
    {
        $this->update([
            'statut' => 'en_examen',
            'code_admin_traitant' => $codeAdmin,
        ]);
    }
}
