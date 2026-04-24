<?php

namespace Database\Factories;

use App\Models\DemandeAdhesion;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class DemandeAdhesionFactory extends Factory
{
    protected $model = DemandeAdhesion::class;

    public function definition()
    {
        return [
            'nom' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'telephone' => '+336'.$this->faker->numberBetween(10000000, 99999999),
            'date_naissance' => $this->faker->date('Y-m-d', '2000-01-01'),
            'lieu_naissance' => $this->faker->city(),
            'adresse' => $this->faker->address(),
            'motivation' => $this->faker->sentence(10),
            'competences' => $this->faker->words(5, true),
            'disponibilite' => $this->faker->randomElement(['Weekends', 'Soirs', 'Flexible']),
            'statut' => 'en_attente',
            'reference_demande' => 'REQ'.strtoupper(Str::random(8)),
            'motif_rejet' => null,
        ];
    }

    public function approved()
    {
        return $this->state(fn (array $attributes) => [
            'statut' => 'approuvee',
        ]);
    }

    public function rejected()
    {
        return $this->state(fn (array $attributes) => [
            'statut' => 'rejetee',
            'motif_rejet' => $this->faker->sentence(5),
        ]);
    }

    public function pending()
    {
        return $this->state(fn (array $attributes) => [
            'statut' => 'en_attente',
        ]);
    }
}
