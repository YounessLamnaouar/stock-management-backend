<?php

namespace Database\Seeders;

use App\Models\Niveau;
use Illuminate\Database\Seeder;

class NiveauSeeder extends Seeder
{
    public function run(): void
    {
        Niveau::create(['nomNiveau' => 'Élevé',  'description' => 'Rupture de stock ou stock critique']);
        Niveau::create(['nomNiveau' => 'Moyen',  'description' => 'Stock très faible (< 50% du seuil)']);
        Niveau::create(['nomNiveau' => 'Faible', 'description' => 'Stock en dessous du seuil minimum']);
    }
}
