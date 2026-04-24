<?php

namespace Database\Factories;

use App\Models\Evenements;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class EvenementsFactory extends Factory
{
    protected $model = Evenements::class;

    public function definition()
    {
        return [
            'code_evenement' => 'EVT' . strtoupper(Str::random(8)),
            'titre' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(3),
            'date_debut' => $this->faker->dateTimeBetween('+1 week', '+3 months'),
            'date_fin' => $this->faker->optional()->dateTimeBetween('+1 week', '+3 months'),
            'lieu' => $this->faker->address(),
            'adresse' => $this->faker->optional()->address(),
            'ville' => $this->faker->optional()->city(),
            'frais_inscription' => $this->faker->randomFloat(2, 0, 50000),
            'places_disponibles' => $this->faker->numberBetween(10, 100),
            'type' => $this->faker->randomElement(['assemblee', 'atelier', 'reunion', 'formation', 'autre']),
            'statut' => 'planifie',
            'image_url' => null,
            'instructions' => $this->faker->optional()->sentence(5),
            'paiement_obligatoire' => $this->faker->boolean(30),
        ];
    }

    public function formation()
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'formation',
        ]);
    }

    public function reunion()
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'reunion',
        ]);
    }

    public function atelier()
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'atelier',
        ]);
    }

    public function assemblee()
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'assemblee',
        ]);
    }

    public function planifie()
    {
        return $this->state(fn (array $attributes) => [
            'statut' => 'planifie',
        ]);
    }

    public function enCours()
    {
        return $this->state(fn (array $attributes) => [
            'statut' => 'en_cours',
        ]);
    }

    public function termine()
    {
        return $this->state(fn (array $attributes) => [
            'statut' => 'termine',
        ]);
    }

    public function free()
    {
        return $this->state(fn (array $attributes) => [
            'frais_inscription' => 0,
        ]);
    }

    public function paid()
    {
        return $this->state(fn (array $attributes) => [
            'frais_inscription' => $this->faker->randomFloat(2, 1000, 50000),
        ]);
    }

    public function upcoming()
    {
        return $this->state(fn (array $attributes) => [
            'date_debut' => $this->faker->dateTimeBetween('+1 week', '+3 months'),
        ]);
    }

    public function past()
    {
        return $this->state(fn (array $attributes) => [
            'date_debut' => $this->faker->dateTimeBetween('-3 months', '-1 week'),
        ]);
    }
}
