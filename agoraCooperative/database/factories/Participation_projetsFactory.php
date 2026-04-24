<?php

namespace Database\Factories;

use App\Models\Membre;
use App\Models\Participation_projets;
use App\Models\Projets;
use Illuminate\Database\Eloquent\Factories\Factory;

class Participation_projetsFactory extends Factory
{
    protected $model = Participation_projets::class;

    public function definition()
    {
        return [
            'projet_id' => Projets::factory(),
            'membre_id' => Membre::factory(),
            'date_participation' => now(),
            'statut' => $this->faker->randomElement(['actif', 'inactif', 'termine']),
            'role' => $this->faker->randomElement(['developpeur', 'designer', 'chef_projet', 'contributeur']),
            'heures_contribuees' => $this->faker->randomFloat(1, 0, 100),
            'taches_realisees' => $this->faker->sentence(5),
            'competences_utilisees' => $this->faker->words(3, true),
        ];
    }

    public function actif()
    {
        return $this->state(fn (array $attributes) => [
            'statut' => 'actif',
        ]);
    }

    public function inactif()
    {
        return $this->state(fn (array $attributes) => [
            'statut' => 'inactif',
        ]);
    }

    public function termine()
    {
        return $this->state(fn (array $attributes) => [
            'statut' => 'termine',
        ]);
    }

    public function developpeur()
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'developpeur',
        ]);
    }

    public function designer()
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'designer',
        ]);
    }

    public function chefProjet()
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'chef_projet',
        ]);
    }

    public function contributeur()
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'contributeur',
        ]);
    }

    public function highHours()
    {
        return $this->state(fn (array $attributes) => [
            'heures_contribuees' => $this->faker->randomFloat(1, 50, 100),
        ]);
    }

    public function lowHours()
    {
        return $this->state(fn (array $attributes) => [
            'heures_contribuees' => $this->faker->randomFloat(1, 0, 20),
        ]);
    }
}
