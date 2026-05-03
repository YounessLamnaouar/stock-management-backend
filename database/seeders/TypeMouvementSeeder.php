<?php

namespace Database\Seeders;

use App\Models\TypeMouvement;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TypeMouvementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TypeMouvement::create([
            'nomType' => 'Entrée'
        ]);

        TypeMouvement::create([
            'nomType' => 'Sortie'
        ]);

        TypeMouvement::create([
            'nomType' => 'Transfert'
        ]);
    }
}
