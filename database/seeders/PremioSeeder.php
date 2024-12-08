<?php

namespace Database\Seeders;

use App\Models\Director;
use App\Models\Pelicula;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PremioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pelicula1 = Pelicula::where('titulo', 'El Gran Escape')->first();
        $pelicula2 = Pelicula::where('titulo', 'Inception')->first();
        $pelicula3 = Pelicula::where('titulo', 'Ready Player One')->first();

        $pelicula1->premios()->createMany([
            [
                'nombre' => 'Oscar',
                'categoria' => 'Mejor Edición',
                'anio' => 1964,
                'imagen' => 'premios/oscar.jpg',
            ],
            [
                'nombre' => 'BAFTA',
                'categoria' => 'Mejor Película',
                'anio' => 1964,
                'imagen' => 'premios/bafta.jpg',
            ],
        ]);

        $pelicula2->premios()->createMany([
            [
                'nombre' => 'Oscar',
                'categoria' => 'Mejor Cinematografía',
                'anio' => 2011,
                'imagen' => 'premios/oscar.jpg',
            ],
            [
                'nombre' => 'Golden Globe',
                'categoria' => 'Mejor Dirección',
                'anio' => 2011,
                'imagen' => 'premios/golden_globe.jpg',
            ],
        ]);

        $pelicula3->premios()->create([
            'nombre' => 'Saturn Award',
            'categoria' => 'Mejor Película de Ciencia Ficción',
            'anio' => 2019,
            'imagen' => 'premios/saturn_award.jpg',
        ]);

        $director1 = Director::where('nombre', 'John Sturges')->first();
        $director2 = Director::where('nombre', 'Christopher Nolan')->first();

        $director1->premios()->create([
            'nombre' => 'Directors Guild of America',
            'categoria' => 'Logro Sobresaliente en Dirección',
            'anio' => 1964,
            'imagen' => 'premios/DGAAward.png',
            'pelicula_id' => 1
        ]);

        $director2->premios()->createMany([
            [
                'nombre' => 'Oscar',
                'categoria' => 'Mejor Guion Original',
                'anio' => 2011,
                'imagen' => 'premios/oscar.jpg',
                'pelicula_id' => 3
            ],
            [
                'nombre' => 'BAFTA',
                'categoria' => 'Mejor Dirección',
                'anio' => 2011,
                'imagen' => 'premios/bafta.jpg',
                'pelicula_id' => 3
            ],
        ]);

        $this->command->info('Premios asociados a películas y directores.');
    }
}
