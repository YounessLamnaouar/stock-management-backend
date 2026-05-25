<?php

namespace Database\Seeders;

use App\Models\Statut;
use Illuminate\Database\Seeder;

class StatutSeeder extends Seeder
{
    public function run(): void
    {
        Statut::create(['nomStatut' => 'Active',   'description' => 'Alerte active, non traitée']);
        Statut::create(['nomStatut' => 'Clôturé',  'description' => 'Alerte clôturée ou résolue']);
    }
}
