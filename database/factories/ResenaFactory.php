<?php

namespace Database\Factories;

use App\Models\Pelicula;
use App\Models\Resena;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ResenaFactory extends Factory
{
    protected $model = Resena::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(), // Crea un usuario si no se especifica
            'pelicula_id' => Pelicula::factory(), // Crea una película si no se especifica
            'comentario' => $this->faker->sentence(),
            'calificacion' => $this->faker->numberBetween(1, 5), // Calificación entre 1 y 5
        ];
    }
}
