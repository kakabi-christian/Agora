<?php

namespace Database\Factories;

use App\Models\Don;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class DonFactory extends Factory
{
    protected $model = Don::class;

    public function definition()
    {
        return [
            'nom_complet' => $this->faker->name(),
            'email' => $this->faker->safeEmail(),
            'telephone' => '+336' . $this->faker->numberBetween(10000000, 99999999),
            'montant' => $this->faker->randomFloat(2, 1000, 100000),
            'mode_paiement' => $this->faker->randomElement(['mobile_money', 'carte_bancaire', 'virement']),
            'message' => $this->faker->sentence(8),
            'statut' => $this->faker->randomElement(['en_attente', 'complet']),
            'reference_paiement' => 'PAY' . strtoupper(Str::random(10)),
        ];
    }

    public function pending()
    {
        return $this->state(fn (array $attributes) => [
            'statut' => 'en_attente',
        ]);
    }

    public function completed()
    {
        return $this->state(fn (array $attributes) => [
            'statut' => 'complet',
        ]);
    }

    public function mobileMoney()
    {
        return $this->state(fn (array $attributes) => [
            'mode_paiement' => 'mobile_money',
        ]);
    }

    public function cardPayment()
    {
        return $this->state(fn (array $attributes) => [
            'mode_paiement' => 'carte_bancaire',
        ]);
    }

    public function bankTransfer()
    {
        return $this->state(fn (array $attributes) => [
            'mode_paiement' => 'virement',
        ]);
    }

    public function smallAmount()
    {
        return $this->state(fn (array $attributes) => [
            'montant' => $this->faker->randomFloat(2, 1000, 10000),
        ]);
    }

    public function largeAmount()
    {
        return $this->state(fn (array $attributes) => [
            'montant' => $this->faker->randomFloat(2, 50000, 100000),
        ]);
    }
}
