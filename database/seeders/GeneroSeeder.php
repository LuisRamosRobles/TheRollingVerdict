<?php

namespace Database\Seeders;

use App\Models\Genero;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GeneroSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Genero::create(['nombre' => 'Acción', 'imagen' => 'generos/1.jpg']);
        Genero::create(['nombre' => 'Comedia', 'imagen' => 'generos/2.jpg']);
        Genero::create(['nombre' => 'Drama', 'imagen' => 'generos/3.jpg']);
        Genero::create(['nombre' => 'Ciencia Ficción', 'imagen' => 'generos/4.jpg']);
        Genero::create(['nombre' => 'Terror', 'imagen' => 'generos/5.jpg']);
        Genero::create(['nombre' => 'Aventura', 'imagen' => 'generos/6.jpg']);
        Genero::create(['nombre' => 'Animación', 'imagen' => 'generos/7.jpg']);
        Genero::create(['nombre' => 'Fantasía', 'imagen' => 'generos/8.jpg']);
        Genero::create(['nombre' => 'Suspense', 'imagen' => 'generos/9.jpg']);
        Genero::create(['nombre' => 'Misterio', 'imagen' => 'generos/10.jpg']);

        /*Genero::create(['nombre' => 'Romance', 'imagen' => 'generos/11.jpg']);
        Genero::create(['nombre' => 'Musical', 'imagen' => 'generos/12.jpg']);
        Genero::create(['nombre' => 'Documental', 'imagen' => 'generos/13.jpg']);
        Genero::create(['nombre' => 'Biografía', 'imagen' => 'generos/14.jpg']);
        Genero::create(['nombre' => 'Histórica', 'imagen' => 'generos/15.jpg']);
        Genero::create(['nombre' => 'Bélica', 'imagen' => 'generos/16.jpg']);
        Genero::create(['nombre' => 'Western', 'imagen' => 'generos/17.jpg']);
        Genero::create(['nombre' => 'Policíaca', 'imagen' => 'generos/18.jpg']);
        Genero::create(['nombre' => 'Deportiva', 'imagen' => 'generos/19.jpg']);
        Genero::create(['nombre' => 'Familiar', 'imagen' => 'generos/20.jpg']);
        Genero::create(['nombre' => 'Infantil', 'imagen' => 'generos/21.jpg']);
        Genero::create(['nombre' => 'Cortometraje', 'imagen' => 'generos/22.jpg']);
        Genero::create(['nombre' => 'Experimental', 'imagen' => 'generos/23.jpg']);
        Genero::create(['nombre' => 'Thriller', 'imagen' => 'generos/24.jpg']);
        Genero::create(['nombre' => 'Noir']);
        Genero::create(['nombre' => 'Crimen']);
        Genero::create(['nombre' => 'Fantástico']);
        Genero::create(['nombre' => 'Superhéroes']);
        Genero::create(['nombre' => 'Melodrama']);
        Genero::create(['nombre' => 'Gore']);
        Genero::create(['nombre' => 'Slasher']);
        Genero::create(['nombre' => 'Psicológico']);
        Genero::create(['nombre' => 'Sobrenatural']);
        Genero::create(['nombre' => 'Survival']);
        Genero::create(['nombre' => 'Distópico']);
        Genero::create(['nombre' => 'Post-apocalíptico']);
        Genero::create(['nombre' => 'Utopía']);
        Genero::create(['nombre' => 'Zombis']);
        Genero::create(['nombre' => 'Vampiros']);
        Genero::create(['nombre' => 'Hombres Lobo']);
        Genero::create(['nombre' => 'Cyberpunk']);
        Genero::create(['nombre' => 'Steampunk']);
        Genero::create(['nombre' => 'Arte y Ensayo']);
        Genero::create(['nombre' => 'Espionaje']);
        Genero::create(['nombre' => 'Judicial']);
        Genero::create(['nombre' => 'Religioso']);
        Genero::create(['nombre' => 'Surrealista']);
        Genero::create(['nombre' => 'Road Movie']);
        Genero::create(['nombre' => 'Pandilleros']);
        Genero::create(['nombre' => 'Detectivesco']);
        Genero::create(['nombre' => 'Satírico']);
        Genero::create(['nombre' => 'Parodia']);
        Genero::create(['nombre' => 'Catástrofes']);
        Genero::create(['nombre' => 'Viajes en el Tiempo']);
        Genero::create(['nombre' => 'Mockumentary']);
        Genero::create(['nombre' => 'Space Opera']);*/

    }
}
