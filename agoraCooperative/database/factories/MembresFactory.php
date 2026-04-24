<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class MembresFactory extends Factory
{
    protected $model = \App\Models\Membres::class;

    public function definition()
    {
        $password = 'tkkc2006'; // mot de passe par défaut
        return [
            'code_membre' => strtoupper($this->faker->unique()->bothify('MBR###')),
            'nom' => $this->faker->lastName(),
            'prenom' => $this->faker->firstName(),
            'email' => $this->faker->unique()->safeEmail(),
            'mot_de_passe' => Hash::make($password), // mot de passe hashé
            'date_inscription' => now(),
            'role' => 'membre', // ou 'administrateur' si tu veux créer un admin
            'est_actif' => true,
            'telephone' => $this->faker->numerify('6########'),
            'adresse' => $this->faker->address(),
            'ville' => $this->faker->city(),
            'code_postal' => $this->faker->numerify('#####'),
            'biographie' => $this->faker->sentence(),
            'photo_url' => null,
        ];
    }
}
