<?php

namespace Database\Factories;

use App\Models\ContactMessage;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ContactMessageFactory extends Factory
{
    protected $model = ContactMessage::class;

    public function definition()
    {
        return [
            'nom' => $this->faker->name(),
            'email' => $this->faker->safeEmail(),
            'telephone' => '+336' . $this->faker->numberBetween(10000000, 99999999),
            'sujet' => $this->faker->sentence(3),
            'message' => $this->faker->paragraph(3),
            'statut' => $this->faker->randomElement(['non_lu', 'lu', 'traite']),
            'reference' => 'MSG' . strtoupper(Str::random(8)),
            'reponse' => null,
            'date_reponse' => null,
        ];
    }

    public function nonLu()
    {
        return $this->state(fn (array $attributes) => [
            'statut' => 'non_lu',
        ]);
    }

    public function lu()
    {
        return $this->state(fn (array $attributes) => [
            'statut' => 'lu',
        ]);
    }

    public function traite()
    {
        return $this->state(fn (array $attributes) => [
            'statut' => 'traite',
            'reponse' => $this->faker->paragraph(2),
            'date_reponse' => $this->faker->dateTimeBetween('-1 week', 'now'),
        ]);
    }

    public function withResponse()
    {
        return $this->state(fn (array $attributes) => [
            'reponse' => $this->faker->paragraph(2),
            'date_reponse' => $this->faker->dateTimeBetween('-1 week', 'now'),
            'statut' => 'traite',
        ]);
    }

    public function urgent()
    {
        return $this->state(fn (array $attributes) => [
            'sujet' => 'URGENT: ' . $this->faker->sentence(3),
        ]);
    }

    public function general()
    {
        return $this->state(fn (array $attributes) => [
            'sujet' => 'General Inquiry',
        ]);
    }

    public function partnership()
    {
        return $this->state(fn (array $attributes) => [
            'sujet' => 'Partnership Proposal',
        ]);
    }
}
