<?php

namespace Database\Factories;

use App\Models\Membre;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class MembreFactory extends Factory
{
    protected $model = Membre::class;

    public function definition()
    {
        return [
            'code_membre' => 'MBR'.strtoupper(Str::random(8)),
            'nom' => $this->faker->lastName(),
            'prenom' => $this->faker->firstName(),
            'email' => $this->faker->unique()->safeEmail(),
            'mot_de_passe' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'date_inscription' => $this->faker->date(),
            'role' => 'membre',
            'est_actif' => true,
            'telephone' => '+336'.$this->faker->numberBetween(10000000, 99999999),
            'adresse' => $this->faker->address(),
            'ville' => $this->faker->city(),
            'code_postal' => $this->faker->postcode(),
            'biographie' => $this->faker->sentence(10),
            'photo_url' => null,
        ];
    }

    public function admin()
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'administrateur',
        ]);
    }

    public function active()
    {
        return $this->state(fn (array $attributes) => [
            'est_actif' => true,
        ]);
    }

    public function inactive()
    {
        return $this->state(fn (array $attributes) => [
            'est_actif' => false,
        ]);
    }
}
