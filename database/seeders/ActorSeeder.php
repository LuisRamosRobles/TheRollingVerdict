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
            'imagen' => 'actores/3.jpg',
        ]);

        $actor3 = Actor::create([
            'nombre' => 'Richard Attenborough',
            'fecha_nac' => '1923-08-29',
            'lugar_nac' => 'Cambridge, Inglaterra',
            'biografia' => 'Actor, director y productor británico, reconocido por su trabajo en cine y televisión.',
            'inicio_actividad' => '1942',
            'fin_actividad' => '2007',
            'activo' => false,
            'imagen' => 'actores/2.jpg',
        ]);

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

        $actor11 = Actor::create([
            'nombre' => 'Margot Robbie',
            'fecha_nac' => '1990-07-02',
            'lugar_nac' => 'Dalby, Queensland, Australia',
            'biografia' => 'Actriz y productora australiana conocida por su versatilidad en papeles cinematográficos.',
            'inicio_actividad' => '2008',
            'activo' => true,
            'imagen' => 'actores/11.jpg',
        ]);

        $actor12 = Actor::create([
            'nombre' => 'Matthew McConaughey',
            'fecha_nac' => '1969-11-04',
            'lugar_nac' => 'Uvalde, Texas, USA',
            'biografia' => 'Actor y productor estadounidense ganador del premio Óscar, conocido por su carisma y papeles memorables en cine y televisión.',
            'inicio_actividad' => '1991',
            'activo' => true,
            'imagen' => 'actores/12.jpg',
        ]);

        $actor13 = Actor::create([
            'nombre' => 'Sandra Bullock',
            'fecha_nac' => '1964-07-26',
            'lugar_nac' => 'Arlington, Virginia, USA',
            'biografia' => 'Actriz y productora estadounidense ganadora del Óscar, conocida por su carisma y papeles icónicos en cine.',
            'inicio_actividad' => '1987',
            'activo' => true,
            'imagen' => 'actores/13.jpg',
        ]);

        $actor14 = Actor::create([
            'nombre' => 'George Clooney',
            'fecha_nac' => '1961-05-06',
            'lugar_nac' => 'Lexington, Kentucky, USA',
            'biografia' => 'Actor, director y productor estadounidense ganador del Óscar, conocido por su elegancia y versatilidad en cine y televisión.',
            'inicio_actividad' => '1978',
            'activo' => true,
            'imagen' => 'actores/14.jpg',
        ]);

        $actor15 = Actor::create([
            'nombre' => 'Ed Harris',
            'fecha_nac' => '1950-11-28',
            'lugar_nac' => 'Tenafly, New Jersey, USA',
            'biografia' => 'Actor y director estadounidense, reconocido por sus interpretaciones intensas y su presencia en películas y televisión.',
            'inicio_actividad' => '1975',
            'activo' => true,
            'imagen' => 'actores/15.jpg',
        ]);

        $actor16 = Actor::create([
            'nombre' => 'John Travolta',
            'fecha_nac' => '1954-02-18',
            'lugar_nac' => 'Englewood, New Jersey, USA',
            'biografia' => 'Actor, cantante y productor estadounidense, famoso por su versatilidad y éxitos en el cine y la televisión.',
            'inicio_actividad' => '1972',
            'activo' => true,
            'imagen' => 'actores/16.jpg',
        ]);

        $actor17 = Actor::create([
            'nombre' => 'Samuel L. Jackson',
            'fecha_nac' => '1948-12-21',
            'lugar_nac' => 'Washington, D.C., USA',
            'biografia' => 'Actor estadounidense con una carrera prolífica, conocido por su carisma y papeles icónicos en una amplia gama de géneros.',
            'inicio_actividad' => '1972',
            'activo' => true,
            'imagen' => 'actores/17.jpg',
        ]);

        $actor18 = Actor::create([
            'nombre' => 'Uma Thurman',
            'fecha_nac' => '1970-04-29',
            'lugar_nac' => 'Boston, Massachusetts, USA',
            'biografia' => 'Actriz y modelo estadounidense, destacada por su elegancia y papeles memorables en el cine.',
            'inicio_actividad' => '1985',
            'activo' => true,
            'imagen' => 'actores/18.jpg',
        ]);

        $actor19 = Actor::create([
            'nombre' => 'Jessica Chastain',
            'fecha_nac' => '1977-03-24',
            'lugar_nac' => 'Sacramento, California, USA',
            'biografia' => 'Actriz y productora estadounidense, destacada por sus interpretaciones profundas y versátiles en cine y televisión.',
            'inicio_actividad' => '2004',
            'activo' => true,
            'imagen' => 'actores/19.jpg',

        ]);

        $actor20 = Actor::create([
            'nombre' => 'Anne Hathaway',
            'fecha_nac' => '1982-11-12',
            'lugar_nac' => 'Brooklyn, New York, USA',
            'biografia' => 'Actriz y cantante estadounidense ganadora del Óscar, conocida por su carisma y talento en una amplia variedad de roles.',
            'inicio_actividad' => '1999',
            'activo' => true,
            'imagen' => 'actores/20.jpg',

        ]);

        $actor21 = Actor::create([
            'nombre' => 'Lucy Liu',
            'fecha_nac' => '1968-12-02',
            'lugar_nac' => 'Queens, New York, USA',
            'biografia' => 'Actriz y directora estadounidense, conocida por su carisma y papeles memorables en cine y televisión.',
            'inicio_actividad' => '1991',
            'activo' => true,
            'imagen' => 'actores/21.jpg',
        ]);

        $actor22 = Actor::create([
            'nombre' => 'David Carradine',
            'fecha_nac' => '1936-12-08',
            'lugar_nac' => 'Hollywood, California, USA',
            'biografia' => 'Actor estadounidense conocido por su trabajo en películas de artes marciales y series icónicas como "Kung Fu".',
            'inicio_actividad' => '1963',
            'fin_actividad' => '2009',
            'activo' => false,
            'imagen' => 'actores/22.jpg',
        ]);

        $actor23 = Actor::create([
            'nombre' => 'Brad Pitt',
            'fecha_nac' => '1963-12-18',
            'lugar_nac' => 'Shawnee, Oklahoma, USA',
            'biografia' => 'Actor y productor estadounidense galardonado con múltiples premios, conocido por su carisma y papeles icónicos en el cine.',
            'inicio_actividad' => '1987',
            'activo' => true,
            'imagen' => 'actores/23.jpg',
        ]);

        $actor24 = Actor::create([
            'nombre' => 'Jeff Goldblum',
            'fecha_nac' => '1952-10-22',
            'lugar_nac' => 'Pittsburgh, Pennsylvania, USA',
            'biografia' => 'Actor estadounidense reconocido por su estilo único y destacadas actuaciones en cine y televisión.',
            'inicio_actividad' => '1974',
            'activo' => true,
            'imagen' => 'actores/24.jpg',

        ]);

        $actor25 = Actor::create([
            'nombre' => 'Laura Dern',
            'fecha_nac' => '1967-02-10',
            'lugar_nac' => 'Los Angeles, California, USA',
            'biografia' => 'Actriz y productora estadounidense ganadora del Óscar, conocida por su talento y compromiso con el arte cinematográfico.',
            'inicio_actividad' => '1973',
            'activo' => true,
            'imagen' => 'actores/25.jpg',

        ]);




        $pelicula1 = Pelicula::where('titulo', 'El Gran Escape')->first();
        $pelicula1->actores()->attach([$actor1->id, $actor2->id, $actor3->id]);

        $pelicula2 = Pelicula::where('titulo', 'Inception')->first();
        $pelicula2->actores()->attach([$actor4->id, $actor5->id, $actor6->id]);

        $pelicula3 = Pelicula::where('titulo', 'Ready Player One')->first();
        $pelicula3->actores()->attach([$actor7->id, $actor8->id, $actor9->id, $actor10->id]);

        $pelicula4 = Pelicula::where('titulo', 'El Lobo de Wall Street')->first();
        $pelicula4->actores()->attach([$actor4->id, $actor11->id, $actor12->id]);

        $pelicula5 = Pelicula::where('titulo', 'Gravity')->first();
        $pelicula5->actores()->attach([$actor13->id, $actor14->id, $actor15->id]);

        $pelicula6 = Pelicula::where('titulo', 'Pulp Fiction')->first();
        $pelicula6->actores()->attach([$actor16->id, $actor17->id, $actor18->id]);

        $pelicula7 = Pelicula::where('titulo', 'Interstellar')->first();
        $pelicula7->actores()->attach([$actor12->id, $actor19->id, $actor20->id]);

        $pelicula8 = Pelicula::where('titulo', 'Kill Bill: Vol. 1')->first();
        $pelicula8->actores()->attach([$actor18->id, $actor21->id, $actor22->id]);

        $pelicula9 = Pelicula::where('titulo', 'Once Upon a Time in Hollywood')->first();
        $pelicula9->actores()->attach([$actor4->id, $actor11->id, $actor23->id]);

        $pelicula10 = Pelicula::where('titulo', 'Jurassic Park')->first();
        $pelicula10->actores()->attach([$actor3->id, $actor24->id, $actor25->id]);
    }
}
