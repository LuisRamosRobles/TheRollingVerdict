<?php

namespace Database\Factories;

use App\Models\Actor;
use App\Models\Director;
use App\Models\Pelicula;
use App\Models\Premio;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Premio>
 */
class PremioFactory extends Factory
{
    protected $model = Premio::class;

    public function definition(): array
    {
        $entidadType = $this->faker->randomElement([Pelicula::class, Director::class, Actor::class]);
        $entidad = $entidadType::factory()->create();

        return [
            'nombre' => $this->faker->randomElement(['Oscar', 'Golden Globe', 'BAFTA']),
            'categoria' => $this->faker->word,
            'anio' => $this->faker->year,
            'entidad_type' => $entidadType,
            'entidad_id' => $entidad->id,
            'pelicula_id' => $entidadType === Pelicula::class ? $entidad->id : Pelicula::factory(),
        ];
    }
}
