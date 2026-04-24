<?php

namespace Database\Factories;

use App\Models\Projets;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjetsFactory extends Factory
{
    protected $model = Projets::class;

    public function definition()
    {
        return [
            'nom' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(3),
            'type' => $this->faker->randomElement(['agricole', 'social', 'environnemental', 'educatif', 'autre']),
            'statut' => 'propose',
            'date_debut' => $this->faker->optional()->date(),
            'date_fin_prevue' => $this->faker->optional()->date(),
            'date_fin_reelle' => null,
            'budget_estime' => $this->faker->randomFloat(2, 10000, 500000),
            'budget_reel' => null,
            'coordinateur' => $this->faker->name(),
            'objectifs' => json_encode([$this->faker->sentence(2), $this->faker->sentence(2)]),
            'resultats' => null,
            'image_url' => null,
            'notes' => $this->faker->optional()->sentence(5),
            'est_public' => $this->faker->boolean(50),
        ];
    }

    public function propose()
    {
        return $this->state(fn (array $attributes) => [
            'statut' => 'propose',
        ]);
    }

    public function enEtude()
    {
        return $this->state(fn (array $attributes) => [
            'statut' => 'en_etude',
        ]);
    }

    public function approuve()
    {
        return $this->state(fn (array $attributes) => [
            'statut' => 'approuve',
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
            'date_fin_reelle' => $this->faker->date(),
        ]);
    }

    public function public()
    {
        return $this->state(fn (array $attributes) => [
            'est_public' => true,
        ]);
    }

    public function prive()
    {
        return $this->state(fn (array $attributes) => [
            'est_public' => false,
        ]);
    }

    public function agricole()
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'agricole',
        ]);
    }

    public function social()
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'social',
        ]);
    }

    public function environnemental()
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'environnemental',
        ]);
    }

    public function educatif()
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'educatif',
        ]);
    }
}
