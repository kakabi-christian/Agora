<?php

namespace Database\Factories;

use App\Models\Faq;
use Illuminate\Database\Eloquent\Factories\Factory;

class FaqFactory extends Factory
{
    protected $model = Faq::class;

    public function definition()
    {
        return [
            'question' => $this->faker->sentence(6) . '?',
            'reponse' => $this->faker->paragraph(2),
            'categorie' => $this->faker->randomElement(['general', 'adhesion', 'evenements', 'projets', 'dons']),
            'ordre' => $this->faker->numberBetween(1, 100),
            'nombre_votes' => $this->faker->numberBetween(0, 50),
            'statut' => 'publie',
        ];
    }

    public function publie()
    {
        return $this->state(fn (array $attributes) => [
            'statut' => 'publie',
        ]);
    }

    public function brouillon()
    {
        return $this->state(fn (array $attributes) => [
            'statut' => 'brouillon',
        ]);
    }

    public function general()
    {
        return $this->state(fn (array $attributes) => [
            'categorie' => 'general',
        ]);
    }

    public function adhesion()
    {
        return $this->state(fn (array $attributes) => [
            'categorie' => 'adhesion',
        ]);
    }

    public function evenements()
    {
        return $this->state(fn (array $attributes) => [
            'categorie' => 'evenements',
        ]);
    }

    public function projets()
    {
        return $this->state(fn (array $attributes) => [
            'categorie' => 'projets',
        ]);
    }

    public function dons()
    {
        return $this->state(fn (array $attributes) => [
            'categorie' => 'dons',
        ]);
    }

    public function popular()
    {
        return $this->state(fn (array $attributes) => [
            'nombre_votes' => $this->faker->numberBetween(20, 50),
        ]);
    }

    public function unpopular()
    {
        return $this->state(fn (array $attributes) => [
            'nombre_votes' => $this->faker->numberBetween(0, 10),
        ]);
    }
}
