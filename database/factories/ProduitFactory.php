<?php

namespace Database\Factories;

use App\Models\Categorie;
use App\Models\Produit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Produit>
 */
class ProduitFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'codeProduit' => strtoupper(fake()->unique()->bothify('PRD###')),
            'nomProduit' => fake()->word(),
            'description' => fake()->sentence(),
            'unite' => 'Pièce',
            'dateCreation' => now(),
            'categorie_id' => Categorie::inRandomOrder()->first()?->id,
        ];
    }
}
