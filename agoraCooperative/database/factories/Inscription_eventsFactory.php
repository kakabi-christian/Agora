<?php

namespace Database\Factories;

use App\Models\Evenements;
use App\Models\Inscription_events;
use App\Models\Membre;
use Illuminate\Database\Eloquent\Factories\Factory;

class Inscription_eventsFactory extends Factory
{
    protected $model = Inscription_events::class;

    public function definition()
    {
        return [
            'evenement_id' => Evenements::factory(),
            'membre_id' => Membre::factory(),
            'date_inscription' => now(),
            'statut' => $this->faker->randomElement(['en_attente', 'confirme', 'annule']),
            'qr_code' => 'QR'.strtoupper($this->faker->unique()->lexify('????????')),
            'date_rappel' => null,
            'rappel_envoye' => false,
        ];
    }

    public function enAttente()
    {
        return $this->state(fn (array $attributes) => [
            'statut' => 'en_attente',
        ]);
    }

    public function confirme()
    {
        return $this->state(fn (array $attributes) => [
            'statut' => 'confirme',
        ]);
    }

    public function annule()
    {
        return $this->state(fn (array $attributes) => [
            'statut' => 'annule',
        ]);
    }

    public function withRappel()
    {
        return $this->state(fn (array $attributes) => [
            'date_rappel' => $this->faker->dateTimeBetween('now', '+1 week'),
            'rappel_envoye' => true,
        ]);
    }

    public function withoutRappel()
    {
        return $this->state(fn (array $attributes) => [
            'date_rappel' => null,
            'rappel_envoye' => false,
        ]);
    }
}
