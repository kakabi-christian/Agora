<?php

namespace Database\Factories;

use App\Models\HistoriqueParticipation;
use App\Models\Membre;
use Illuminate\Database\Eloquent\Factories\Factory;

class HistoriqueParticipationFactory extends Factory
{
    protected $model = HistoriqueParticipation::class;

    public function definition()
    {
        return [
            'membre_id' => Membre::factory(),
            'type_participation' => $this->faker->randomElement(['evenement', 'projet', 'don', 'formation']),
            'description' => $this->faker->sentence(5),
            'date_participation' => $this->faker->dateTimeBetween('-6 months', 'now'),
            'duree_heures' => $this->faker->randomFloat(1, 0, 40),
            'statut' => $this->faker->randomElement(['complet', 'en_cours', 'annule']),
        ];
    }

    public function evenement()
    {
        return $this->state(fn (array $attributes) => [
            'type_participation' => 'evenement',
        ]);
    }

    public function projet()
    {
        return $this->state(fn (array $attributes) => [
            'type_participation' => 'projet',
        ]);
    }

    public function don()
    {
        return $this->state(fn (array $attributes) => [
            'type_participation' => 'don',
        ]);
    }

    public function formation()
    {
        return $this->state(fn (array $attributes) => [
            'type_participation' => 'formation',
        ]);
    }

    public function complet()
    {
        return $this->state(fn (array $attributes) => [
            'statut' => 'complet',
        ]);
    }

    public function enCours()
    {
        return $this->state(fn (array $attributes) => [
            'statut' => 'en_cours',
        ]);
    }

    public function annule()
    {
        return $this->state(fn (array $attributes) => [
            'statut' => 'annule',
        ]);
    }

    public function longDuration()
    {
        return $this->state(fn (array $attributes) => [
            'duree_heures' => $this->faker->randomFloat(1, 20, 40),
        ]);
    }

    public function shortDuration()
    {
        return $this->state(fn (array $attributes) => [
            'duree_heures' => $this->faker->randomFloat(1, 0, 10),
        ]);
    }

    public function recent()
    {
        return $this->state(fn (array $attributes) => [
            'date_participation' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ]);
    }

    public function old()
    {
        return $this->state(fn (array $attributes) => [
            'date_participation' => $this->faker->dateTimeBetween('-6 months', '-3 months'),
        ]);
    }
}
