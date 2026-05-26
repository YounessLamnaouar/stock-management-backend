<?php

namespace Database\Factories;

use App\Models\Entrepot;
use App\Models\MovementStock;
use App\Models\Produit;
use App\Models\StatusMouvement;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MovementStock>
 */
class MovementStockFactory extends Factory
{
    public function definition(): array
    {
        return [
            'dateMouvement'           => now(),
            'quantite'                => fake()->numberBetween(1, 100),
            'user_id'                 => User::inRandomOrder()->first()?->id,
            'produit_id'              => Produit::inRandomOrder()->first()?->id,
            'status_mouvement_id'     => StatusMouvement::inRandomOrder()->first()?->id,
            'entrepot_source_id'      => Entrepot::inRandomOrder()->first()?->id,
            'entrepot_destination_id' => Entrepot::inRandomOrder()->first()?->id,
        ];
    }
}
