<?php

namespace Database\Factories;

use App\Models\Entrepot;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Entrepot>
 */
class EntrepotFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nomEntrepot' => 'Entrepot ' . fake()->city(),
            'adresse' => fake()->address(),
            'ville' => fake()->city(),
            'capacite' => fake()->numberBetween(100, 1000),
        ];
    }
}
