<?php

namespace Database\Seeders;

use App\Models\Pelicula;
use App\Models\Resena;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class ResenaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Instanciar Faker
        $faker = Faker::create();

        // Obtener todas las películas y usuarios
        $peliculas = Pelicula::all();
        $usuarios = User::all();

        foreach ($usuarios as $usuario) {
            foreach ($peliculas as $pelicula) {
                Resena::create([
                    'user_id' => $usuario->id,
                    'pelicula_id' => $pelicula->id,
                    'calificacion' => rand(1, 5),
                    'comentario' => $faker->sentence,
                ]);
            }
        }
    }
}
