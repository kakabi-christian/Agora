<?php

namespace Database\Factories;

use App\Models\Partenaire;
use Illuminate\Database\Eloquent\Factories\Factory;

class PartenaireFactory extends Factory
{
    protected $model = Partenaire::class;

    public function definition()
    {
        return [
            'nom' => $this->faker->company(),
            'description' => $this->faker->paragraph(2),
            'type_partenaire' => $this->faker->randomElement(['technologique', 'financier', 'institutionnel', 'media']),
            'logo_url' => null,
            'site_web' => $this->faker->url(),
            'contact_email' => $this->faker->companyEmail(),
            'contact_telephone' => '+336'.$this->faker->numberBetween(10000000, 99999999),
            'adresse' => $this->faker->address(),
            'statut' => 'actif',
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

    public function technologique()
    {
        return $this->state(fn (array $attributes) => [
            'type_partenaire' => 'technologique',
        ]);
    }

    public function financier()
    {
        return $this->state(fn (array $attributes) => [
            'type_partenaire' => 'financier',
        ]);
    }

    public function institutionnel()
    {
        return $this->state(fn (array $attributes) => [
            'type_partenaire' => 'institutionnel',
        ]);
    }

    public function media()
    {
        return $this->state(fn (array $attributes) => [
            'type_partenaire' => 'media',
        ]);
    }
}
