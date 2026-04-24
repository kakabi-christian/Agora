<?php

namespace Database\Factories;

use App\Models\Ressource;
use Illuminate\Database\Eloquent\Factories\Factory;

class RessourceFactory extends Factory
{
    protected $model = Ressource::class;

    public function definition()
    {
        return [
            'titre' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(2),
            'type_ressource' => $this->faker->randomElement(['pdf', 'word', 'excel', 'powerpoint', 'video', 'image']),
            'categorie' => $this->faker->randomElement(['documentation', 'template', 'guide', 'rapport', 'formation']),
            'chemin_fichier' => 'ressources/'.$this->faker->word().'.'.$this->faker->fileExtension(),
            'taille_fichier' => $this->faker->numberBetween(1024, 10485760), // 1KB to 10MB
            'nombre_telechargements' => $this->faker->numberBetween(0, 500),
            'statut' => $this->faker->randomElement(['publie', 'brouillon']),
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

    public function pdf()
    {
        return $this->state(fn (array $attributes) => [
            'type_ressource' => 'pdf',
            'chemin_fichier' => 'ressources/'.$this->faker->word().'.pdf',
        ]);
    }

    public function word()
    {
        return $this->state(fn (array $attributes) => [
            'type_ressource' => 'word',
            'chemin_fichier' => 'ressources/'.$this->faker->word().'.docx',
        ]);
    }

    public function excel()
    {
        return $this->state(fn (array $attributes) => [
            'type_ressource' => 'excel',
            'chemin_fichier' => 'ressources/'.$this->faker->word().'.xlsx',
        ]);
    }

    public function powerpoint()
    {
        return $this->state(fn (array $attributes) => [
            'type_ressource' => 'powerpoint',
            'chemin_fichier' => 'ressources/'.$this->faker->word().'.pptx',
        ]);
    }

    public function video()
    {
        return $this->state(fn (array $attributes) => [
            'type_ressource' => 'video',
            'chemin_fichier' => 'ressources/'.$this->faker->word().'.mp4',
        ]);
    }

    public function documentation()
    {
        return $this->state(fn (array $attributes) => [
            'categorie' => 'documentation',
        ]);
    }

    public function template()
    {
        return $this->state(fn (array $attributes) => [
            'categorie' => 'template',
        ]);
    }

    public function guide()
    {
        return $this->state(fn (array $attributes) => [
            'categorie' => 'guide',
        ]);
    }

    public function rapport()
    {
        return $this->state(fn (array $attributes) => [
            'categorie' => 'rapport',
        ]);
    }

    public function formation()
    {
        return $this->state(fn (array $attributes) => [
            'categorie' => 'formation',
        ]);
    }

    public function popular()
    {
        return $this->state(fn (array $attributes) => [
            'nombre_telechargements' => $this->faker->numberBetween(100, 500),
        ]);
    }

    public function unpopular()
    {
        return $this->state(fn (array $attributes) => [
            'nombre_telechargements' => $this->faker->numberBetween(0, 50),
        ]);
    }

    public function smallFile()
    {
        return $this->state(fn (array $attributes) => [
            'taille_fichier' => $this->faker->numberBetween(1024, 1048576), // 1KB to 1MB
        ]);
    }

    public function largeFile()
    {
        return $this->state(fn (array $attributes) => [
            'taille_fichier' => $this->faker->numberBetween(5242880, 10485760), // 5MB to 10MB
        ]);
    }
}
