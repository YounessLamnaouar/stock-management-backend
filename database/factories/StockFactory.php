<?php

namespace Database\Factories;

use App\Models\Entrepot;
use App\Models\Produit;
use App\Models\Stock;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Stock>
 */
class StockFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'quantiteDisponible' => fake()->numberBetween(0, 500),
            'seuilMin' => fake()->numberBetween(5, 20),
            'dateDerniereMaj' => now(),
            'produit_id' => Produit::inRandomOrder()->first()?->id,
            'entrepot_id' => Entrepot::inRandomOrder()->first()?->id,
        ];
    }
}
