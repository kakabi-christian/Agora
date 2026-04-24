<?php

namespace App\Services;

use App\Models\Membre;

class CodeMembreGenerator
{
    /**
     * Générer un code membre unique au format MBR-YYYY-XXXXX
     * 
     * @return string
     */
    public static function generate(): string
    {
        $year = date('Y');
        
        // Trouver le dernier code membre de l'année
        $lastMembre = Membre::where('code_membre', 'like', "MBR-{$year}-%")
            ->orderBy('code_membre', 'desc')
            ->first();
        
        // Extraire le numéro de séquence et incrémenter
        $sequence = $lastMembre 
            ? intval(substr($lastMembre->code_membre, -5)) + 1 
            : 1;
        
        // Générer le code au format MBR-YYYY-XXXXX
        $codeMembre = sprintf("MBR-%s-%05d", $year, $sequence);
        
        // Vérifier l'unicité (au cas où)
        while (Membre::where('code_membre', $codeMembre)->exists()) {
            $sequence++;
            $codeMembre = sprintf("MBR-%s-%05d", $year, $sequence);
        }
        
        return $codeMembre;
    }
}
