<?php

namespace Database\Seeders;

use App\Models\StatusMouvement;
use Illuminate\Database\Seeder;

class StatusMouvementSeeder extends Seeder
{
    public function run(): void
    {
        $statuts = ['En cours', 'Validée', 'Annulée'];

        foreach ($statuts as $nom) {
            StatusMouvement::firstOrCreate(['nomStatus' => $nom]);
        }
    }
}
