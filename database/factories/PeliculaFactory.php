<?php

namespace Database\Factories;

use App\Models\Director;
use App\Models\Pelicula;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pelicula>
 */
class PeliculaFactory extends Factory
{

    protected $model = Pelicula::class;


    public function definition(): array
    {
        return [
            'titulo' => $this->faker->sentence,
            'estreno' => $this->faker->date,
            'director_id' => Director::factory(),
            'sinopsis' => $this->faker->paragraph,
            'imagen' => Pelicula::$IMAGEN_DEFAULT  // Imagen de ejemplo
        ];
    }
}
