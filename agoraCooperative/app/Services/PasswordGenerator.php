<?php

namespace App\Services;

class PasswordGenerator
{
    /**
     * Générer un mot de passe temporaire sécurisé
     * Format: 1 majuscule + 1 minuscule + 1 chiffre + caractères aléatoires (min 8)
     * 
     * @param int $length
     * @return string
     */
    public static function generate(int $length = 12): string
    {
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $numbers = '0123456789';
        $special = '!@#$%&*';
        
        // Garantir au moins un de chaque type requis
        $password = '';
        $password .= $uppercase[random_int(0, strlen($uppercase) - 1)];
        $password .= $lowercase[random_int(0, strlen($lowercase) - 1)];
        $password .= $numbers[random_int(0, strlen($numbers) - 1)];
        
        // Remplir le reste avec des caractères aléatoires
        $allChars = $uppercase . $lowercase . $numbers . $special;
        for ($i = strlen($password); $i < $length; $i++) {
            $password .= $allChars[random_int(0, strlen($allChars) - 1)];
        }
        
        // Mélanger les caractères
        return str_shuffle($password);
    }
}
