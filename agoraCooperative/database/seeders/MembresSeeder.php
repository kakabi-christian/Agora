<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Membre;

class MembresSeeder extends Seeder
{
    public function run()
    {
        // Créer 10 membres aléatoires
        // \App\Models\Membres::factory(10)->create();

        // Créer un admin avec email fixe pour se connecter
        Membre::create([
            'code_membre' => 'ADMIN001',
            'nom' => 'kakabi',
            'prenom' => 'christian',
            'email' => 'kakabichristian58@gmail.com',
            'mot_de_passe' => \Illuminate\Support\Facades\Hash::make('tkkc2006'), // mot de passe connu
            'date_inscription' => now(),
            'role' => 'administrateur',
            'est_actif' => true,
            'telephone' => '658877445',
            'adresse' => 'Elf',
            'ville' => 'Douala',
            'code_postal' => '00000',
        ]);
    }
}
// php artisan db:seed --class=MembresSeeder