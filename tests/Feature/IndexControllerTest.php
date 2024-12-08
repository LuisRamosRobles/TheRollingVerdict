<?php

namespace Tests\Feature;

use App\Models\Pelicula;
use App\Models\Resena;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class IndexControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_devuelve_vista_con_peliculas_y_usuario()
    {

        $user = User::factory()->create();


        $this->actingAs($user);


        $peliculas = Pelicula::factory()
            ->count(6)
            ->has(Resena::factory()->count(3), 'resenas')
            ->create();


        $peliculas->each(function ($pelicula, $index) {
            $pelicula->resenas->each(function ($resena) use ($index) {
                $resena->update(['calificacion' => 5 - $index % 5]);
            });
        });


        $response = $this->get(route('index'));


        $response->assertStatus(200);


        $response->assertViewIs('index');


        $response->assertViewHas('peliculas', function ($viewPeliculas) use ($peliculas) {
            $esperadas = $peliculas
                ->sortByDesc(fn($pelicula) => $pelicula->promedio_calificacion)
                ->take(4)
                ->pluck('id')
                ->toArray();

            return $viewPeliculas->pluck('id')->toArray() === $esperadas;
        });


        $response->assertViewHas('usuario', fn($viewUsuario) => $viewUsuario->id === Auth::id());
    }

    public function test_index_funciona_para_usuarios_no_autenticados()
    {

        $peliculas = Pelicula::factory()
            ->count(4)
            ->has(Resena::factory()->count(3), 'resenas')
            ->create();


        $response = $this->get(route('index'));


        $response->assertStatus(200);


        $response->assertViewIs('index');


        $response->assertViewHas('peliculas', function ($viewPeliculas) use ($peliculas) {
            return $viewPeliculas->pluck('id')->diff($peliculas->pluck('id'))->isEmpty();
        });


        $response->assertViewHas('usuario', null);
    }
}
