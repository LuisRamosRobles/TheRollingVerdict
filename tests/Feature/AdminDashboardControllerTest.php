<?php

namespace Tests\Feature;

use App\Models\Actor;
use App\Models\Director;
use App\Models\Genero;
use App\Models\Pelicula;
use App\Models\Premio;
use App\Models\Resena;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    private function createAdminUser()
    {
        return User::factory()->create(['role' => 'ADMIN']);
    }

    public function test_index_muestra_dashboard_para_admin()
    {
        $admin = $this->createAdminUser();
        $this->actingAs($admin);

        $response = $this->get(route('admin.dashboard'));

        $response->assertStatus(200)
            ->assertViewIs('admin.dashboard');
    }

    public function test_index_no_accesible_para_usuarios_no_admin()
    {
        $user = User::factory()->create(['role' => 'USER']);
        $this->actingAs($user);

        $response = $this->get(route('admin.dashboard'));

        $response->assertStatus(403);
    }

    public function test_peliculas_muestra_peliculas()
    {
        $admin = $this->createAdminUser();
        $this->actingAs($admin);

        $peliculas = Pelicula::factory()->count(3)->create();

        $response = $this->get(route('admin.peliculas'));

        $response->assertStatus(200)
            ->assertViewIs('admin.peliculas.index')
            ->assertViewHas('peliculas', function ($viewPeliculas) use ($peliculas) {
                return $viewPeliculas->pluck('id')->diff($peliculas->pluck('id'))->isEmpty();
            });
    }

    public function test_generos_muestra_generos()
    {
        $admin = $this->createAdminUser();
        $this->actingAs($admin);

        $generos = Genero::factory()->count(3)->create();

        $response = $this->get(route('admin.generos'));

        $response->assertStatus(200)
            ->assertViewIs('admin.generos.index')
            ->assertViewHas('generos', function ($viewGeneros) use ($generos) {
                return $viewGeneros->pluck('id')->diff($generos->pluck('id'))->isEmpty();
            });
    }

    public function test_directores_muestra_directores()
    {
        $admin = $this->createAdminUser();
        $this->actingAs($admin);

        $directores = Director::factory()->count(3)->create();

        $response = $this->get(route('admin.directores'));

        $response->assertStatus(200)
            ->assertViewIs('admin.directores.index')
            ->assertViewHas('directores', function ($viewDirectores) use ($directores) {
                return $viewDirectores->pluck('id')->diff($directores->pluck('id'))->isEmpty();
            });
    }

    public function test_actores_muestra_actores()
    {
        $admin = $this->createAdminUser();
        $this->actingAs($admin);

        $actores = Actor::factory()->count(3)->create();

        $response = $this->get(route('admin.actores'));

        $response->assertStatus(200)
            ->assertViewIs('admin.actores.index')
            ->assertViewHas('actores', function ($viewActores) use ($actores) {
                return $viewActores->pluck('id')->diff($actores->pluck('id'))->isEmpty();
            });
    }

    public function test_premios_muestra_premios()
    {
        $admin = $this->createAdminUser();
        $this->actingAs($admin);

        $premios = Premio::factory()->count(3)->create();

        $response = $this->get(route('admin.premios'));

        $response->assertStatus(200)
            ->assertViewIs('admin.premios.index')
            ->assertViewHas('premios', function ($viewPremios) use ($premios) {
                return $viewPremios->pluck('id')->diff($premios->pluck('id'))->isEmpty();
            });
    }

    public function test_resenas_muestra_peliculas_con_resenas()
    {
        $admin = $this->createAdminUser();
        $this->actingAs($admin);

        $peliculaConResenas = Pelicula::factory()->has(Resena::factory()->count(3))->create();
        $peliculaSinResenas = Pelicula::factory()->create();

        $response = $this->get(route('admin.resenas'));

        $response->assertStatus(200)
            ->assertViewIs('admin.resenas.index')
            ->assertViewHas('peliculas', function ($peliculas) use ($peliculaSinResenas, $peliculaConResenas) {
                return $peliculas->pluck('id')->contains($peliculaConResenas->id) &&
                    !$peliculas->pluck('id')->contains($peliculaSinResenas->id);
            });
    }

    public function test_resenasPorPelicula_muestra_resenas_de_una_pelicula()
    {
        $admin = $this->createAdminUser();
        $this->actingAs($admin);

        $pelicula = Pelicula::factory()->create();
        $resenas = Resena::factory()->count(3)->create(['pelicula_id' => $pelicula->id]);

        $response = $this->get(route('admin.resenas.show', $pelicula->id));

        $response->assertStatus(200)
            ->assertViewIs('admin.resenas.show')
            ->assertViewHas('pelicula', function ($viewPelicula) use ($pelicula) {
                return $viewPelicula->id === $pelicula->id;
            });

        foreach ($resenas as $resena) {
            $response->assertSee($resena->comentario);
        }
    }


}
