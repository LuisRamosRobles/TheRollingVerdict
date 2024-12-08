<?php

namespace Tests\Feature;


use App\Models\Actor;
use App\Models\Director;
use App\Models\Genero;
use App\Models\Pelicula;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;


class PeliculaControllerTest extends TestCase
{
    use RefreshDatabase;

    private function createAdminUser()
    {
        return User::factory()->create(['role' => 'ADMIN']);
    }

    private function createUser()
    {
        return User::factory()->create(['role' => 'USER']);
    }

    private function createDirector()
    {
        return Director::factory()->create();
    }

    private function createGeneros($count = 1)
    {
        return Genero::factory()->count($count)->create();
    }

    private function createActores($count = 1)
    {
        return Actor::factory()->count($count)->create();
    }

    private function createPeliculas($count = 1, $attributes = [])
    {
        $director = $this->createDirector();
        return Pelicula::factory()->count($count)->create(array_merge(['director_id' => $director->id], $attributes));
    }


    public function test_peliculas_index_devuelve_vista_con_peliculas()
    {
        $this->createDirector();
        $this->createPeliculas(5);

        $response = $this->get(route('peliculas.index'));

        $response->assertStatus(200)
                 ->assertViewIs('peliculas.index')
                 ->assertViewHas('peliculas');

    }

    public function test_peliculas_show_devuelve_vista_con_pelicula()
    {
        $pelicula = $this->createPeliculas()->first();

        $response = $this->get(route('peliculas.show', $pelicula->id));

        $response->assertStatus(200)
            ->assertViewIs('peliculas.show')
            ->assertViewHas('pelicula', fn ($viewPelicula) => $viewPelicula->id === $pelicula->id);
    }

    public function test_create_devuelve_vista_para_usuarios_admin()
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)->get(route('peliculas.create'));

        $response->assertStatus(200)
                 ->assertViewIs('peliculas.create');

    }

    public function test_create_acceso_restringido_para_usuarios_no_admin()
    {
        $user = $this->createUser();

        $response = $this->actingAs($user)->get(route('peliculas.create'));

        $response->assertStatus(403);
    }

    public function test_create_acceso_restringido_para_usuarios_no_registrados()
    {
        $response = $this->get(route('peliculas.create'));

        $response->assertRedirect(route('login'));
    }

    public function test_store_crea_pelicula_para_usuarios_admin()
    {
        $admin = $this->createAdminUser();

        $director = $this->createDirector();

        $generos = $this->createGeneros(2);
        $actores = $this->createActores(2);

        $data = [
            'titulo' => 'TituloPrueba',
            'estreno' => '2024-01-01',
            'director_id' => $director->id,
            'sinopsis' => 'Esto es una prueba de la sinopsis',
            'generos' => $generos->pluck('id')->toArray(),
            'reparto' => $actores->pluck('id')->toArray(),
        ];

        $response = $this->actingAs($admin)->post(route('peliculas.store'), $data);

        $response->assertRedirect(route('peliculas.show', Pelicula::latest('id')->first()->id));

        $this->assertDatabaseHas('peliculas', [
            'titulo' => 'TituloPrueba',
            'estreno' => '2024-01-01',
            'director_id' => $director->id,
            'sinopsis' => 'Esto es una prueba de la sinopsis',
        ]);

        $pelicula = Pelicula::latest('id')->first();
        $this->assertTrue($pelicula->generos->pluck('id')->contains($generos->first()->id));
        $this->assertTrue($pelicula->actores->pluck('id')->contains($actores->first()->id));
    }


    public function test_store_no_crea_pelicula_para_usuarios_no_admin()
    {
        $user = $this->createUser();

        $director = $this->createDirector();


        $data = [
            'titulo' => 'TituloPrueba',
            'estreno' => '2024-01-01',
            'director_id' => $director->id,
            'sinopsis' => 'Esto es una prueba de la sinopsis',

        ];

        $response = $this->actingAs($user)->post(route('peliculas.store'), $data);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('peliculas', ['titulo' => 'TituloPrueba']);
    }

    public function test_store_no_crea_pelicula_para_usuarios_no_registrados()
    {
        $director = $this->createDirector();


        $data = [
            'titulo' => 'TituloPrueba',
            'estreno' => '2024-01-01',
            'director_id' => $director->id,
            'sinopsis' => 'Esto es una prueba de la sinopsis',

        ];

        $response = $this->post(route('peliculas.store'), $data);

        $response->assertRedirect(route('login'));
        $this->assertDatabaseMissing('peliculas', ['titulo' => 'TituloPrueba']);

    }

    public function test_edit_devuelve_vista_para_usuarios_admin()
    {
        $admin = $this->createAdminUser();
        $pelicula = $this->createPeliculas()->first();

        $response = $this->actingAs($admin)->get(route('peliculas.edit', $pelicula->id));

        $response->assertStatus(200)
                 ->assertViewIs('peliculas.edit')
                 ->assertViewHasAll(['pelicula', 'generos', 'directores',
                     'repartoSeleccionado', 'actoresDisponibles']);
    }

    public function test_edit_no_accesible_para_usuarios_no_admin()
    {
        $user = $this->createUser();
        $pelicula = $this->createPeliculas()->first();

        $response = $this->actingAs($user)->get(route('peliculas.edit', $pelicula->id));

        $response->assertStatus(403);
    }

    public function test_edit_no_accesible_para_usuarios_no_registrados()
    {
        $pelicula = $this->createPeliculas()->first();

        $response = $this->get(route('peliculas.edit', $pelicula->id));

        $response->assertRedirect(route('login'));
    }

    public function test_update_actualiza_pelicula_para_usuarios_admin()
    {
        $admin = $this->createAdminUser();
        $pelicula = $this->createPeliculas()->first();
        $director = $this->createDirector();
        $genero = $this->createGeneros()->first();
        $actores = $this->createActores(2);

        $data = [
            'titulo' => 'Nuevo Titulo',
            'estreno' => '2023-05-01',
            'director_id' => $director->id,
            'sinopsis' => 'Nueva sinopsis actualizada',
            'generos' => [$genero->id],
            'reparto' => $actores->pluck('id')->toArray(),
        ];

        $response = $this->actingAs($admin)->patch(route('peliculas.update', $pelicula->id), $data);

        $response->assertRedirect(route('peliculas.show', $pelicula->id));

        $this->assertDatabaseHas('peliculas', [
            'id' => $pelicula->id,
            'titulo' => 'Nuevo Titulo',
            'estreno' => '2023-05-01',
            'director_id' => $director->id,
            'sinopsis' => 'Nueva sinopsis actualizada',
        ]);

        $pelicula = Pelicula::find($pelicula->id);
        $this->assertTrue($pelicula->generos->pluck('id')->contains($genero->id));
        $this->assertTrue($pelicula->actores->pluck('id')->contains($actores->first()->id));
    }

    public function test_update_no_permitido_para_usuarios_no_admin()
    {
        $user = $this->createUser();
        $pelicula = $this->createPeliculas()->first();
        $data = [
            'titulo' => 'Nuevo Titulo',
            'estreno' => '2023-05-01',
        ];

        $response = $this->actingAs($user)->patch(route('peliculas.update', $pelicula->id), $data);

        $response->assertStatus(403);
    }

    public function test_update_no_permitido_para_usuarios_no_registrados()
    {
        $pelicula = $this->createPeliculas()->first();
        $data = [
            'titulo' => 'Nuevo Titulo',
            'estreno' => '2023-05-01',
        ];

        $response = $this->patch(route('peliculas.update', $pelicula->id), $data);

        $response->assertRedirect(route('login'));
    }

    public function test_destroy_elimina_pelicula_para_usuarios_admin()
    {
        $admin = $this->createAdminUser();
        $pelicula = $this->createPeliculas()->first();

        $response = $this->actingAs($admin)->delete(route('peliculas.destroy', $pelicula->id));

        $response->assertRedirect(route('admin.peliculas'));

        $this->assertSoftDeleted('peliculas', ['id' => $pelicula->id]);
    }

    public function test_destroy_no_permitido_para_usuarios_user()
    {
        $user = $this->createUser();
        $pelicula = $this->createPeliculas()->first();

        $response = $this->actingAs($user)->delete(route('peliculas.destroy', $pelicula->id));

        $response->assertStatus(403);
        $this->assertDatabaseHas('peliculas', ['id' => $pelicula->id, 'deleted_at' => null]);
    }

    public function test_destroy_no_permitido_para_usuarios_no_autenticados()
    {
        $pelicula = $this->createPeliculas()->first();

        $response = $this->delete(route('peliculas.destroy', $pelicula->id));

        $response->assertRedirect(route('login'));
        $this->assertDatabaseHas('peliculas', ['id' => $pelicula->id, 'deleted_at' => null]);
    }

    public function test_deleted_muestra_peliculas_eliminadas_para_usuarios_admin()
    {
        $admin = $this->createAdminUser();
        $peliculasActivas = $this->createPeliculas(3);
        $peliculasEliminadas = $this->createPeliculas(3);
        $peliculasEliminadas->each->delete();

        $response = $this->actingAs($admin)->get(route('peliculas.deleted'));

        $response->assertStatus(200)
                 ->assertViewIs('peliculas.deleted');


        $response->assertViewHas('peliculas', function ($peliculas) use ($peliculasEliminadas) {
            return $peliculas->pluck('id')->diff($peliculasEliminadas->pluck('id'))->isEmpty();
        });

        $response->assertViewHas('peliculas', function ($peliculas) use ($peliculasActivas) {
            return $peliculas->pluck('id')->intersect($peliculasActivas->pluck('id'))->isEmpty();
        });

        $indexResponse = $this->actingAs($admin)->get(route('peliculas.index'));

        $indexResponse->assertStatus(200)
            ->assertViewIs('peliculas.index');

        $indexResponse->assertViewHas('peliculas', function ($peliculas) use ($peliculasActivas) {
            return $peliculas->pluck('id')->diff($peliculasActivas->pluck('id'))->isEmpty();
        });

        $indexResponse->assertViewHas('peliculas', function ($peliculas) use ($peliculasEliminadas) {
            return $peliculas->pluck('id')->intersect($peliculasEliminadas->pluck('id'))->isEmpty();
        });
    }

    public function test_deleted_no_permitido_para_usuarios_user()
    {
        $user = $this->createUser();

        $response = $this->actingAs($user)->get(route('peliculas.deleted'));

        $response->assertStatus(403);
    }

    public function test_deleted_no_permitido_para_usuarios_no_autenticados()
    {
        $response = $this->get(route('peliculas.deleted'));

        $response->assertRedirect(route('login'));
    }

    public function test_restore_restaurar_pelicula_eliminada_para_usuarios_admin()
    {
        $admin = $this->createAdminUser();
        $pelicula = $this->createPeliculas()->first();
        $pelicula->delete();

        $response = $this->actingAs($admin)->post(route('peliculas.restore', $pelicula->id));

        $response->assertRedirect(route('peliculas.deleted'));


        $this->assertDatabaseHas('peliculas', [
            'id' => $pelicula->id,
            'deleted_at' => null,
        ]);

    }

    public function test_restore_no_permitido_para_usuarios_user()
    {
        $user = $this->createUser();
        $pelicula = $this->createPeliculas()->first();
        $pelicula->delete();

        $response = $this->actingAs($user)->post(route('peliculas.restore', $pelicula->id));

        $response->assertStatus(403);
        $this->assertSoftDeleted('peliculas', ['id' => $pelicula->id]);
    }

    public function test_restore_no_permitido_para_usuarios_no_autenticados()
    {
        $pelicula = $this->createPeliculas()->first();
        $pelicula->delete();

        $response = $this->post(route('peliculas.restore', $pelicula->id));

        $response->assertRedirect(route('login'));
        $this->assertSoftDeleted('peliculas', ['id' => $pelicula->id]);
    }
}
