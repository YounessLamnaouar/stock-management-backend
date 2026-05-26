<?php

namespace Database\Seeders;

use App\Models\Categorie;
use App\Models\Entrepot;
use App\Models\MovementStock;
use App\Models\Produit;
use App\Models\Stock;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            StatusMouvementSeeder::class,
        ]);

        User::create([
            'name'     => 'Admin',
            'prenom'   => 'Super',
            'email'    => 'admin@gmail.com',
            'password' => bcrypt('123456'),
            'role_id'  => 1,
        ]);

        User::create([
            'name'     => 'Gestionnaire',
            'prenom'   => 'Marie',
            'email'    => 'gestionnaire@gmail.com',
            'password' => bcrypt('123456'),
            'role_id'  => 2,
        ]);

        User::create([
            'name'     => 'Agent',
            'prenom'   => 'Ahmed',
            'email'    => 'agent@gmail.com',
            'password' => bcrypt('123456'),
            'role_id'  => 3,
        ]);

        Categorie::factory(10)->create();
        Produit::factory(50)->create();
        Entrepot::factory(5)->create();
        Stock::factory(100)->create();
        MovementStock::factory(200)->create();
    }
}
