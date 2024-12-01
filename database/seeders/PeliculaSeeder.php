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

        $directorPelicula1_id = 1;
        $directorPelicula2_id = 3;
        $directorPelicula3_id = 2;

        $pelicula1 = Pelicula::create([
            'titulo' => 'El Gran Escape',
            'estreno' => '1963-07-04',
            'director_id' => $directorPelicula1_id,
            'sinopsis' => 'Un grupo de prisioneros intenta escapar de un campo de prisioneros durante la Segunda Guerra Mundial.',
            'imagen' => 'peliculas/1.jpg'
        ]);

        $pelicula2 = Pelicula::create([
            'titulo' => 'Inception',
            'estreno' => '2010-07-16',
            'director_id' => $directorPelicula2_id,
            'sinopsis' => 'Un ladrón que roba secretos a través de la tecnología de sueños se enfrenta a un último trabajo.',
            'imagen' => 'peliculas/2.jpg'
        ]);

        $pelicula3 = Pelicula::create([
            'titulo' => 'Ready Player One',
            'estreno' => '2018-03-29',
            'director_id' => $directorPelicula3_id,
            'sinopsis' => 'En un futuro distópico, un joven se embarca en una búsqueda dentro de un mundo de realidad virtual para encontrar un valioso "Easter egg" escondido por su creador.',
            'imagen' => 'peliculas/3.jpg'
        ]);


        $pelicula1->generos()->sync([1, 3]); // IDs de géneros: Acción y Drama
        $pelicula2->generos()->sync([4, 1]);
        $pelicula3->generos()->sync([1, 2, 3, 4]); // IDs de géneros: Acción, Aventura, Drama y Fantasía
    }
}
