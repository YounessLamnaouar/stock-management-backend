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
    public function definition(): array
    {
        return [
            'quantite'      => fake()->numberBetween(0, 500),
            'dateMiseAJour' => now(),
            'produit_id'    => Produit::inRandomOrder()->first()?->id,
            'entrepot_id'   => Entrepot::inRandomOrder()->first()?->id,
        ];
    }
}
