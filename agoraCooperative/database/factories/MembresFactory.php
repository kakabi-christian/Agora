<?php

namespace Database\Factories;

use App\Models\Membre;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class MembresFactory extends Factory
{
    protected $model = Membre::class;

    /**
     * Le mot de passe statique hashé pour optimiser les performances des tests.
     */
    protected static ?string $password;

    public function definition()
    {
        return [
            'code_membre' => strtoupper($this->faker->unique()->bothify('MBR###')),
            'nom' => $this->faker->lastName(),
            'prenom' => $this->faker->firstName(),
            'email' => $this->faker->unique()->safeEmail(),
            // Utilisation d'une variable statique pour éviter de hasher à chaque itération
            // et suppression du mot de passe en clair "tkkc2006"
            'mot_de_passe' => static::$password ??= Hash::make('password'), 
            'date_inscription' => now(),
            'role' => 'membre',
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