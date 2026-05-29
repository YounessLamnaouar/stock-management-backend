<?php

namespace Database\Factories;

use App\Models\Categorie;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Categorie>
 */
class CategorieFactory extends Factory
{
    public function definition(): array
    {
        static $names = [
            'Électronique', 'Mobilier', 'Alimentaire', 'Textile', 'Chimie',
            'Mécanique', 'Informatique', 'Papeterie', 'Sanitaire', 'Outillage',
        ];
        static $index = 0;

        return [
            'nomCategorie' => $names[$index++ % count($names)],
        ];
    }
}
