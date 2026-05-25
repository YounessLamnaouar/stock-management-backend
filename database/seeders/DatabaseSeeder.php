<?php

namespace Database\Seeders;

use App\Models\Categorie;
use App\Models\Entrepot;
use App\Models\MovementStock;
use App\Models\Produit;
use App\Models\Stock;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $this->call([
            RoleSeeder::class,
            TypeMouvementSeeder::class,
            StatutSeeder::class,
            NiveauSeeder::class,
        ]);

        // Default admin account
        User::create([
            'name'     => 'Admin',
            'prenom'   => 'Super',
            'email'    => 'admin@gmail.com',
            'password' => bcrypt('123456'),
            'role_id'  => 1,
            'statut'   => 'Actif',
        ]);

        // Sample gestionnaire
        User::create([
            'name'     => 'Gestionnaire',
            'prenom'   => 'Marie',
            'email'    => 'gestionnaire@gmail.com',
            'password' => bcrypt('123456'),
            'role_id'  => 2,
            'statut'   => 'Actif',
        ]);

        // Sample agent
        User::create([
            'name'     => 'Agent',
            'prenom'   => 'Ahmed',
            'email'    => 'agent@gmail.com',
            'password' => bcrypt('123456'),
            'role_id'  => 3,
            'statut'   => 'Actif',
        ]);

        Categorie::factory(10)->create();
        Produit::factory(50)->create();
        Entrepot::factory(5)->create();
        Stock::factory(100)->create();
        MovementStock::factory(200)->create();
    }
}
