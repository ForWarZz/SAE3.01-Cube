<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Commercial;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Commercial::create([
            'nom_commercial' => 'Jean',
            'prenom_commercial' => 'Marc',
            'email_commercial' => 'jean.marc@cube.fr',
            'hash_mdp_commercial' => bcrypt('password123'),
        ]);
    }
}
