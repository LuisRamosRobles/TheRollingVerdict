<?php

namespace Database\Seeders;

use App\Models\Pelicula;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PeliculaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $director_id1 = 1; //John Sturges
        $director_id2 = 2; //Steven Spielberg
        $director_id3 = 3; //Christopher Nolan
        $director_id4 = 4; //Martin Scorsese
        $director_id5 = 5; //Alfonso Cuarón
        $director_id6 = 6; //Quentin Tarantino


        $pelicula1 = Pelicula::create([
            'titulo' => 'El Gran Escape',
            'estreno' => '1963-07-04',
            'director_id' => $director_id1,
            'sinopsis' => 'Un grupo de prisioneros intenta escapar de un campo de prisioneros durante la Segunda Guerra Mundial.',
            'imagen' => 'peliculas/1.jpg'
        ]);

        $pelicula2 = Pelicula::create([
            'titulo' => 'Ready Player One',
            'estreno' => '2018-03-29',
            'director_id' => $director_id2,
            'sinopsis' => 'En un futuro distópico, un joven se embarca en una búsqueda dentro de un mundo de realidad virtual para encontrar un valioso "Easter egg" escondido por su creador.',
            'imagen' => 'peliculas/2.jpg'
        ]);

        $pelicula3 = Pelicula::create([
            'titulo' => 'Inception',
            'estreno' => '2010-07-16',
            'director_id' => $director_id3,
            'sinopsis' => 'Un ladrón que roba secretos a través de la tecnología de sueños se enfrenta a un último trabajo.',
            'imagen' => 'peliculas/3.jpg'
        ]);

        $pelicula4 =  Pelicula::create([
            'titulo' => 'El Lobo de Wall Street',
            'estreno' => '2014-01-17',
            'director_id' => $director_id4,
            'sinopsis' => 'Un ambicioso corredor de bolsa, Jordan Belfort, asciende meteóricamente en Wall Street a través del fraude y el exceso, mientras su vida se desmorona en un torbellino de avaricia y decadencia.',
            'imagen' => 'peliculas/4.jpg'
        ]);

        $pelicula5 =  Pelicula::create([
            'titulo' => 'Gravity',
            'estreno' => '2013-10-04',
            'director_id' => $director_id5,
            'sinopsis' => 'Dos astronautas quedan a la deriva en el espacio tras un accidente catastrófico, luchando por sobrevivir mientras enfrentan la inmensidad y la soledad del cosmos.',
            'imagen' => 'peliculas/5.jpg'
        ]);

        $pelicula6 =  Pelicula::create([
            'titulo' => 'Pulp Fiction',
            'estreno' => '1994-01-13',
            'director_id' => $director_id6,
            'sinopsis' => 'Historias entrelazadas de crimen, redención y violencia siguen a gánsteres, boxeadores y delincuentes en un oscuro pero vibrante retrato del mundo criminal de Los Ángeles.',
            'imagen' => 'peliculas/6.jpg'
        ]);

        $pelicula7 = Pelicula::create([
            'titulo' => 'Interstellar',
            'estreno' => '2014-11-07',
            'director_id' => $director_id3,
            'sinopsis' => 'Un grupo de exploradores espaciales se embarca en una misión para salvar a la humanidad, viajando a través de un agujero de gusano en busca de un nuevo hogar.',
            'imagen' => 'peliculas/7.jpg',
        ]);

        $pelicula8 = Pelicula::create([
            'titulo' => 'Kill Bill: Vol. 1',
            'estreno' => '2003-10-10',
            'director_id' => $director_id6,
            'sinopsis' => 'Una asesina despierta de un coma y emprende una sangrienta venganza contra aquellos que la traicionaron y destruyeron su vida.',
            'imagen' => 'peliculas/8.jpg',

        ]);

        $pelicula9 = Pelicula::create([
            'titulo' => 'Once Upon a Time in Hollywood',
            'estreno' => '2019-07-26',
            'director_id' => $director_id6, // Cambia este ID al que corresponda con Quentin Tarantino
            'sinopsis' => 'Un actor en declive y su doble de acción navegan la industria cinematográfica de Los Ángeles de 1969 mientras sus vidas se cruzan con los infames eventos de la familia Manson.',
            'imagen' => 'peliculas/9.jpg',

        ]);

        $pelicula10 = Pelicula::create([
            'titulo' => 'Jurassic Park',
            'estreno' => '1993-06-11',
            'director_id' => $director_id2, // Cambia este ID al que corresponda con Steven Spielberg
            'sinopsis' => 'Un parque temático con dinosaurios clonados se convierte en un caos cuando las criaturas escapan, poniendo en peligro a los visitantes y al personal.',
            'imagen' => 'peliculas/10.jpg',

        ]);



        $pelicula1->generos()->sync([1, 3]);
        $pelicula2->generos()->sync([1, 4]);
        $pelicula3->generos()->sync([4, 1]);
        $pelicula4->generos()->sync([2, 9]);
        $pelicula5->generos()->sync([4, 9]);
        $pelicula6->generos()->sync([9]);
        $pelicula7->generos()->sync([4, 6]);
        $pelicula8->generos()->sync([1, 9]);
        $pelicula9->generos()->sync([2, 9]);
        $pelicula10->generos()->sync([6, 4]);

    }
}
