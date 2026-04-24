<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParametreSysteme extends Model
{
    use HasFactory;

    protected $table = 'parametres_systeme';

    protected $fillable = [
        'cle',
        'valeur',
        'type',
        'groupe',
        'description',
    ];

    // Méthode helper pour récupérer une valeur
    public static function get($cle, $default = null)
    {
        $parametre = self::where('cle', $cle)->first();
        return $parametre ? $parametre->valeur : $default;
    }

    // Méthode helper pour définir une valeur
    public static function set($cle, $valeur, $type = 'string', $groupe = 'general', $description = null)
    {
        return self::updateOrCreate(
            ['cle' => $cle],
            [
                'valeur' => $valeur,
                'type' => $type,
                'groupe' => $groupe,
                'description' => $description,
            ]
        );
    }
}
