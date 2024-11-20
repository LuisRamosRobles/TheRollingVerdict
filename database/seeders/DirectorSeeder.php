<?php

namespace Database\Seeders;

use App\Models\Director;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DirectorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Director::create([
            'nombre' => 'John Sturges',
            'fecha_nac' => '1910-01-03',
            'lugar_nac' => 'Oak Park, Illinois, Estados Unidos',
            'biografia' => 'Director estadounidense famoso por sus películas de acción y westerns, como "The Great Escape" y "The Magnificent Seven".',
            'inicio_actividad' => '1932-01-01',
            'activo' => 'false',
            'imagen' => 'directores/1.jpg'
        ]);


        Director::create([
            'nombre' => 'Steven Spielberg',
            'fecha_nac' => '1946-12-18',
            'lugar_nac' => 'Cincinnati, Ohio, USA',
            'biografia' => 'Director de cine y productor estadounidense, conocido por películas icónicas como "Jurassic Park" y "Schindler\'s List".',
            'inicio_actividad' => '1971-01-01',
            'activo' => 'true',
            'imagen' => 'directores/2.jpg'
        ]);

        Director::create([
            'nombre' => 'Christopher Nolan',
            'fecha_nac' => '1970-07-30',
            'lugar_nac' => 'Londres, Inglaterra',
            'biografia' => 'Director británico conocido por películas de alto concepto como "Inception" y la trilogía de "The Dark Knight".',
            'inicio_actividad' => '1998-01-01',
            'activo' => 'true',
            'imagen' => 'directores/3.jpg'
        ]);

        Director::create([
            'nombre' => 'Martin Scorsese',
            'fecha_nac' => '1942-11-17',
            'lugar_nac' => 'Queens, Nueva York, USA',
            'biografia' => 'Reconocido director estadounidense famoso por películas como "Goodfellas" y "The Wolf of Wall Street".',
            'inicio_actividad' => '1963-01-01',
            'activo' => 'true',
            'imagen' => 'directores/4.jpg'
        ]);

        Director::create([
            'nombre' => 'Alfonso Cuarón',
            'fecha_nac' => '1961-11-28',
            'lugar_nac' => 'Ciudad de México, México',
            'biografia' => 'Director mexicano conocido por películas como "Gravity" y "Roma".',
            'inicio_actividad' => '1983-01-01',
            'activo' => 'true',
            'imagen' => 'directores/5.jpg'
        ]);

        Director::create([
            'nombre' => 'Quentin Tarantino',
            'fecha_nac' => '1963-03-27',
            'lugar_nac' => 'Knoxville, Tennessee, USA',
            'biografia' => 'Director conocido por su estilo único y películas como "Pulp Fiction" y "Django Unchained".',
            'inicio_actividad' => '1987-01-01',
            'activo' => 'true',
            'imagen' => 'directores/6.jpg'
        ]);
    }
}
