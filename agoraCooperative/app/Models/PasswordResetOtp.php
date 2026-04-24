<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasswordResetOtp extends Model
{
    use HasFactory;

    // On précise le nom de la table
    protected $table = 'password_reset_otps';

    // On désactive les timestamps automatiques (car on n'a pas de updated_at)
    public $timestamps = false;

    // Les champs que l'on peut remplir
    protected $fillable = [
        'email',
        'otp',
        'created_at'
    ];

    // On définit created_at comme une date pour faciliter les calculs d'expiration
    protected $casts = [
        'created_at' => 'datetime',
    ];
}