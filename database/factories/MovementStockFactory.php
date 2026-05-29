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
        $entrepots  = Entrepot::pluck('id')->toArray();
        $sourceId   = count($entrepots) > 1 ? fake()->randomElement($entrepots) : null;
        $destId     = collect($entrepots)->reject(fn($id) => $id === $sourceId)->first();

        return [
            'dateMouvement'           => fake()->dateTimeBetween('-30 days', 'now')->format('Y-m-d'),
            'quantite'                => fake()->numberBetween(1, 50),
            'user_id'                 => User::inRandomOrder()->first()?->id ?? 1,
            'produit_id'              => Produit::inRandomOrder()->first()?->id ?? 1,
            'status_mouvement_id'     => StatusMouvement::inRandomOrder()->first()?->id ?? 1,
            'entrepot_source_id'      => $sourceId,
            'entrepot_destination_id' => $destId,
        ];
    }
}
