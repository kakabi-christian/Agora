<?php

namespace Database\Factories;

use App\Models\Notification;
use Illuminate\Database\Eloquent\Factories\Factory;

class NotificationFactory extends Factory
{
    protected $model = Notification::class;

    public function definition()
    {
        return [
            'titre' => $this->faker->sentence(3),
            'contenu' => $this->faker->paragraph(2),
            'type' => $this->faker->randomElement(['system', 'event', 'project', 'payment', 'personal']),
            'destinataire_type' => $this->faker->randomElement(['all', 'membre', 'admin']),
            'destinataire_id' => $this->faker->optional()->numberBetween(1, 100),
            'lue' => $this->faker->boolean(30), // 30% chance of being read
            'date_envoi' => $this->faker->dateTimeBetween('-1 week', '+1 week'),
        ];
    }

    public function system()
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'system',
        ]);
    }

    public function event()
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'event',
        ]);
    }

    public function project()
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'project',
        ]);
    }

    public function payment()
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'payment',
        ]);
    }

    public function personal()
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'personal',
        ]);
    }

    public function forAll()
    {
        return $this->state(fn (array $attributes) => [
            'destinataire_type' => 'all',
            'destinataire_id' => null,
        ]);
    }

    public function forMembre()
    {
        return $this->state(fn (array $attributes) => [
            'destinataire_type' => 'membre',
        ]);
    }

    public function forAdmin()
    {
        return $this->state(fn (array $attributes) => [
            'destinataire_type' => 'admin',
        ]);
    }

    public function lu()
    {
        return $this->state(fn (array $attributes) => [
            'lue' => true,
        ]);
    }

    public function nonLu()
    {
        return $this->state(fn (array $attributes) => [
            'lue' => false,
        ]);
    }

    public function sent()
    {
        return $this->state(fn (array $attributes) => [
            'date_envoi' => $this->faker->dateTimeBetween('-1 week', 'now'),
        ]);
    }

    public function scheduled()
    {
        return $this->state(fn (array $attributes) => [
            'date_envoi' => $this->faker->dateTimeBetween('now', '+1 week'),
        ]);
    }

    public function urgent()
    {
        return $this->state(fn (array $attributes) => [
            'titre' => 'URGENT: ' . $this->faker->sentence(3),
            'type' => 'system',
        ]);
    }
}
