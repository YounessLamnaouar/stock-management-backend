<?php

namespace Database\Factories;

use App\Models\Entrepot;
use App\Models\MovementStock;
use App\Models\Produit;
use App\Models\TypeMouvement;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MovementStock>
 */
class MovementStockFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'dateMouvement' => now(),
            'quantite' => fake()->numberBetween(1, 100),
            'commentaire' => fake()->sentence(),
            'user_id' => User::inRandomOrder()->first()?->id,
            'produit_id' => Produit::inRandomOrder()->first()?->id,
            'type_mouvement_id' => TypeMouvement::inRandomOrder()->first()?->id,
            'entrepot_source_id' => Entrepot::inRandomOrder()->first()?->id,
            'entrepot_destination_id' => Entrepot::inRandomOrder()->first()?->id,
        ];
    }
}
