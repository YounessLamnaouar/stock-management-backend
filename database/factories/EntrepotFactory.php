<?php

namespace Database\Factories;

use App\Models\Entrepot;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Entrepot>
 */
class EntrepotFactory extends Factory
{
    public function definition(): array
    {
        $entrepots = [
            ['nom' => 'Entrepôt Casablanca', 'adresse' => 'Zone Industrielle Sidi Maarouf, Casablanca'],
            ['nom' => 'Entrepôt Rabat',      'adresse' => 'Zone Industrielle Ain Aouda, Rabat'],
            ['nom' => 'Entrepôt Marrakech',  'adresse' => 'Zone Industrielle Sidi Ghanem, Marrakech'],
            ['nom' => 'Entrepôt Tanger',     'adresse' => 'Zone Franche de Tanger, Tanger'],
            ['nom' => 'Entrepôt Fès',        'adresse' => 'Zone Industrielle Saïss, Fès'],
        ];

        static $index = 0;
        $e = $entrepots[$index++ % count($entrepots)];

        return [
            'nomEntrepot' => $e['nom'],
            'adresse'     => $e['adresse'],
            'capaciteMax' => fake()->numberBetween(500, 2000),
        ];
    }
}
