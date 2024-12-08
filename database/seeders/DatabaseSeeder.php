<?php

namespace Database\Seeders;

use App\Models\Genero;
use App\Models\Pelicula;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            GeneroSeeder::class,
            DirectorSeeder::class,
            PeliculaSeeder::class,
            ActorSeeder::class,
            PremioSeeder::class,
            ResenaSeeder::class,
        ]);



        // User::factory(10)->create();

        /*
         * User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
         * */
    }
}
