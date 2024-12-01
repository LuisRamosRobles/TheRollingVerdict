<?php

namespace Database\Seeders;

use App\Models\Actor;
use App\Models\Pelicula;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ActorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Actores de "El Gran Escape"
        $actor1 = Actor::create([
            'nombre' => 'Steve McQueen',
            'fecha_nac' => '1930-03-24',
            'lugar_nac' => 'Beech Grove, Indiana, USA',
            'biografia' => 'Actor estadounidense conocido como "The King of Cool".',
            'inicio_actividad' => '1952',
            'fin_actividad' => '1980',
            'activo' => false,
            'imagen' => 'actores/1.jpg',
        ]);

        $actor2 = Actor::create([
            'nombre' => 'James Garner',
            'fecha_nac' => '1928-04-07',
            'lugar_nac' => 'Norman, Oklahoma, USA',
            'biografia' => 'Actor y productor estadounidense conocido por su versatilidad.',
            'inicio_actividad' => '1956',
            'fin_actividad' => '2014',
            'activo' => false,
            'imagen' => 'actores/2.jpg',
        ]);

        $actor3 = Actor::create([
            'nombre' => 'Richard Attenborough',
            'fecha_nac' => '1923-08-29',
            'lugar_nac' => 'Cambridge, Inglaterra',
            'biografia' => 'Actor, director y productor británico, reconocido por su trabajo en cine y televisión.',
            'inicio_actividad' => '1942',
            'fin_actividad' => '2007',
            'activo' => false,
            'imagen' => 'actores/3.jpg',
        ]);

        $pelicula1 = Pelicula::where('titulo', 'El Gran Escape')->first();
        $pelicula1->actores()->attach([$actor1->id, $actor2->id, $actor3->id]);

        // Actores de "Inception"
        $actor4 = Actor::create([
            'nombre' => 'Leonardo DiCaprio',
            'fecha_nac' => '1974-11-11',
            'lugar_nac' => 'Los Ángeles, California, USA',
            'biografia' => 'Actor y productor de renombre internacional.',
            'inicio_actividad' => '1991',
            'activo' => true,
            'imagen' => 'actores/4.jpg',
        ]);

        $actor5 = Actor::create([
            'nombre' => 'Joseph Gordon-Levitt',
            'fecha_nac' => '1981-02-17',
            'lugar_nac' => 'Los Ángeles, California, USA',
            'biografia' => 'Actor y cineasta estadounidense con una carrera diversa.',
            'inicio_actividad' => '1988',
            'activo' => true,
            'imagen' => 'actores/5.jpg',
        ]);

        $actor6 = Actor::create([
            'nombre' => 'Tom Hardy',
            'fecha_nac' => '1977-09-15',
            'lugar_nac' => 'Hammersmith, Londres, Inglaterra',
            'biografia' => 'Actor británico conocido por su versatilidad y papeles en películas de gran éxito.',
            'inicio_actividad' => '2001',
            'activo' => true,
            'imagen' => 'actores/6.jpg',
        ]);

        $pelicula2 = Pelicula::where('titulo', 'Inception')->first();
        $pelicula2->actores()->attach([$actor4->id, $actor5->id, $actor6->id]);

        // Actores de "Ready Player One"
        $actor7 = Actor::create([
            'nombre' => 'Tye Sheridan',
            'fecha_nac' => '1996-11-11',
            'lugar_nac' => 'Elkhart, Texas, USA',
            'biografia' => 'Actor joven y prometedor con una carrera en ascenso.',
            'inicio_actividad' => '2011',
            'activo' => true,
            'imagen' => 'actores/7.jpg',
        ]);

        $actor8 = Actor::create([
            'nombre' => 'Olivia Cooke',
            'fecha_nac' => '1993-12-27',
            'lugar_nac' => 'Oldham, Inglaterra',
            'biografia' => 'Actriz británica reconocida por sus papeles en cine y televisión.',
            'inicio_actividad' => '2012',
            'activo' => true,
            'imagen' => 'actores/8.jpg',
        ]);

        $actor9 = Actor::create([
            'nombre' => 'Ben Mendelsohn',
            'fecha_nac' => '1969-04-03',
            'lugar_nac' => 'Melbourne, Victoria, Australia',
            'biografia' => 'Actor australiano conocido por su intensidad en la actuación.',
            'inicio_actividad' => '1984',
            'activo' => true,
            'imagen' => 'actores/9.jpg',
        ]);

        $actor10 = Actor::create([
            'nombre' => 'Lena Waithe',
            'fecha_nac' => '1984-05-17',
            'lugar_nac' => 'Chicago, Illinois, USA',
            'biografia' => 'Guionista, productora y actriz estadounidense.',
            'inicio_actividad' => '2011',
            'activo' => true,
            'imagen' => 'actores/10.jpg',
        ]);

        $pelicula3 = Pelicula::where('titulo', 'Ready Player One')->first();
        $pelicula3->actores()->attach([$actor7->id, $actor8->id, $actor9->id, $actor10->id]);
    }
}
