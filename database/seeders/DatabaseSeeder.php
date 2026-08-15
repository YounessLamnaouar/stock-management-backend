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

        User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name'     => 'Admin',
                'prenom'   => 'Super',
                'password' => bcrypt('123456'),
                'role_id'  => 1,
            ]
        );

        User::firstOrCreate(
            ['email' => 'gestionnaire@gmail.com'],
            [
                'name'     => 'Gestionnaire',
                'prenom'   => 'Marie',
                'password' => bcrypt('123456'),
                'role_id'  => 2,
            ]
        );

        User::firstOrCreate(
            ['email' => 'agent@gmail.com'],
            [
                'name'     => 'Agent',
                'prenom'   => 'Ahmed',
                'password' => bcrypt('123456'),
                'role_id'  => 3,
            ]
        );

        Categorie::factory(10)->create();
        Produit::factory(50)->create();
        Entrepot::factory(5)->create();
        Stock::factory(100)->create();
        MovementStock::factory(200)->create();
    }
}
