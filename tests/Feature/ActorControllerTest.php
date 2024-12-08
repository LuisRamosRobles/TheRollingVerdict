<?php

namespace Tests\Feature;

use App\Models\Actor;
use App\Models\Pelicula;
use App\Models\Premio;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ActorControllerTest extends TestCase
{
    use RefreshDatabase;

    private function createActor($attributes = [])
    {
        return Actor::factory()->create($attributes);
    }

    private function createActores($count = 1, $attributes = [])
    {
        return Actor::factory()->count($count)->create($attributes);
    }

    private function createPeliculas($count = 1, $attributes = [])
    {
        return Pelicula::factory()->count($count)->create($attributes);
    }

    private function createPremios($count = 1, $attributes = [])
    {
        $defaultAttributes = [
            'entidad_type' => Actor::class,
            'entidad_id' => $attributes['actor_id'] ?? $this->createActor()->id,
        ];

        unset($attributes['actor_id']);

        return Premio::factory()->count($count)->create(array_merge($defaultAttributes, $attributes));
    }

    private function createAdminUser()
    {
        return User::factory()->create(['role' => 'ADMIN']);
    }

    private function createUser()
    {
        return User::factory()->create(['role' => 'USER']);
    }

    public function test_index_devuelve_vista_con_actores()
    {
        $actores = $this->createActores(5);

        $response = $this->get(route('actores.index'));

        $response->assertStatus(200)
            ->assertViewIs('actores.index')
            ->assertViewHas('actores', function ($viewActores) use ($actores) {
                return $viewActores->pluck('id')->diff($actores->pluck('id'))->isEmpty();
            });
    }

    public function test_show_devuelve_vista_con_actor_y_sus_peliculas_y_premios()
    {
        $actor = $this->createActor();
        $peliculas = $this->createPeliculas(3);
        $premios = $this->createPremios(2, ['entidad_id' => $actor->id, 'entidad_type' => Actor::class]);

        $actor->peliculas()->sync($peliculas->pluck('id')->toArray());

        $response = $this->get(route('actores.show', $actor->id));

        $response->assertStatus(200)
            ->assertViewIs('actores.show')
            ->assertViewHas('actor', fn($viewActor) => $viewActor->id === $actor->id)
            ->assertViewHas('peliculas', function ($viewPeliculas) use ($peliculas) {
                return $viewPeliculas->pluck('id')->diff($peliculas->pluck('id'))->isEmpty();
            });


        $this->assertTrue(
            $actor->premios->pluck('id')->diff($premios->pluck('id'))->isEmpty(),
            'Los premios no están asociados correctamente al actor.'
        );
    }

    public function test_create_devuelve_vista_para_usuarios_admin()
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)->get(route('actores.create'));

        $response->assertStatus(200)
            ->assertViewIs('actores.create')
            ->assertViewHas('actor', fn($viewActor) => $viewActor instanceof \App\Models\Actor);
    }

    public function test_create_no_permitido_para_usuarios_no_admin()
    {
        $user = $this->createUser();

        $response = $this->actingAs($user)->get(route('actores.create'));

        $response->assertStatus(403);
    }

    public function test_create_no_permitido_para_usuarios_no_autenticados()
    {
        $response = $this->get(route('actores.create'));

        $response->assertRedirect(route('login'));
    }

    public function test_store_crea_actor_para_usuarios_admin()
    {
        $admin = $this->createAdminUser();

        $data = [
            'nombre' => 'Brad Pitt',
            'fecha_nac' => '1963-12-18',
            'lugar_nac' => 'Shawnee, Oklahoma, USA',
            'biografia' => 'Actor de renombre mundial.',
            'inicio_actividad' => 1987,
            'fin_actividad' => null,
            'activo' => true,
        ];

        $response = $this->actingAs($admin)->post(route('actores.store'), $data);

        $response->assertRedirect(route('actores.show', Actor::latest('id')->first()->id));

        $this->assertDatabaseHas('actores', [
            'nombre' => 'Brad Pitt',
            'fecha_nac' => '1963-12-18',
            'lugar_nac' => 'Shawnee, Oklahoma, USA',
            'biografia' => 'Actor de renombre mundial.',
            'inicio_actividad' => 1987,
            'activo' => true,
        ]);
    }

    public function test_store_no_crea_actor_con_datos_invalidos()
    {
        $admin = $this->createAdminUser();

        $data = [
            'nombre' => '',
            'fecha_nac' => 'invalid-date',
        ];

        $response = $this->actingAs($admin)->post(route('actores.store'), $data);

        $response->assertSessionHasErrors(['nombre', 'fecha_nac']);
        $this->assertDatabaseMissing('actores', ['nombre' => '']);
    }

    public function test_store_no_permitido_para_usuarios_no_admin()
    {
        $user = $this->createUser();

        $data = [
            'nombre' => 'Brad Pitt',
            'fecha_nac' => '1963-12-18',
        ];

        $response = $this->actingAs($user)->post(route('actores.store'), $data);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('actores', ['nombre' => 'Brad Pitt']);
    }

    public function test_store_no_permitido_para_usuarios_no_autenticados()
    {
        $data = [
            'nombre' => 'Brad Pitt',
            'fecha_nac' => '1963-12-18',
        ];

        $response = $this->post(route('actores.store'), $data);

        $response->assertRedirect(route('login'));
        $this->assertDatabaseMissing('actores', ['nombre' => 'Brad Pitt']);
    }

    public function test_edit_devuelve_vista_para_usuarios_admin()
    {
        $admin = $this->createAdminUser();
        $actor = $this->createActor();
        $peliculas = $this->createPeliculas(3);

        $response = $this->actingAs($admin)->get(route('actores.edit', $actor->id));

        $response->assertStatus(200)
            ->assertViewIs('actores.edit')
            ->assertViewHas('actor', fn($viewActor) => $viewActor->id === $actor->id)
            ->assertViewHas('peliculas', fn($viewPeliculas) => $viewPeliculas->pluck('id')->diff($peliculas->pluck('id'))->isEmpty());
    }

    public function test_edit_no_permitido_para_usuarios_no_admin()
    {
        $user = $this->createUser();
        $actor = $this->createActor();

        $response = $this->actingAs($user)->get(route('actores.edit', $actor->id));

        $response->assertStatus(403);
    }

    public function test_edit_no_permitido_para_usuarios_no_autenticados()
    {
        $actor = $this->createActor();

        $response = $this->get(route('actores.edit', $actor->id));

        $response->assertRedirect(route('login'));
    }

    public function test_update_actualiza_actor_para_usuarios_admin()
    {
        $admin = $this->createAdminUser();
        $actor = $this->createActor([
            'nombre' => 'Leonardo DiCaprio',
            'inicio_actividad' => 1990,
        ]);

        $peliculas = $this->createPeliculas(3);

        $data = [
            'nombre' => 'Robert Downey Jr.',
            'fecha_nac' => '1965-04-04',
            'lugar_nac' => 'Nueva York, USA',
            'biografia' => 'Actor mundialmente conocido.',
            'inicio_actividad' => 1985,
            'fin_actividad' => null,
            'activo' => true,
            'reparto' => $peliculas->pluck('id')->toArray(),
        ];

        $response = $this->actingAs($admin)->patch(route('actores.update', $actor->id), $data);

        $response->assertRedirect(route('actores.show', $actor->id));


        $this->assertDatabaseHas('actores', [
            'id' => $actor->id,
            'nombre' => 'Robert Downey Jr.',
            'fecha_nac' => '1965-04-04',
            'lugar_nac' => 'Nueva York, USA',
            'inicio_actividad' => 1985,
        ]);


        $actor->refresh();
        $this->assertTrue(
            $actor->peliculas->pluck('id')->diff($peliculas->pluck('id'))->isEmpty(),
            'Las películas no se relacionaron correctamente con el actor.'
        );
    }


    public function test_update_no_actualiza_actor_con_datos_invalidos()
    {
        $admin = $this->createAdminUser();
        $actor = $this->createActor(['nombre' => 'Leonardo DiCaprio']);

        $data = [
            'nombre' => '',
            'fecha_nac' => 'invalid-date',
        ];

        $response = $this->actingAs($admin)->patch(route('actores.update', $actor->id), $data);

        $response->assertSessionHasErrors(['nombre', 'fecha_nac']);
        $this->assertDatabaseHas('actores', ['id' => $actor->id, 'nombre' => 'Leonardo DiCaprio']);
    }

    public function test_update_no_permitido_para_usuarios_no_admin()
    {
        $user = $this->createUser();
        $actor = $this->createActor();

        $data = [
            'nombre' => 'Robert Downey Jr.',
        ];

        $response = $this->actingAs($user)->patch(route('actores.update', $actor->id), $data);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('actores', ['nombre' => 'Robert Downey Jr.']);
    }

    public function test_update_no_permitido_para_usuarios_no_autenticados()
    {
        $actor = $this->createActor();

        $data = [
            'nombre' => 'Robert Downey Jr.',
        ];

        $response = $this->patch(route('actores.update', $actor->id), $data);

        $response->assertRedirect(route('login'));
        $this->assertDatabaseMissing('actores', ['nombre' => 'Robert Downey Jr.']);
    }

    public function test_destroy_elimina_actor_para_usuarios_admin()
    {
        $admin = $this->createAdminUser();
        $actor = $this->createActor();

        $response = $this->actingAs($admin)->delete(route('actores.destroy', $actor->id));

        $response->assertRedirect(route('admin.actores'));
        $this->assertSoftDeleted('actores', ['id' => $actor->id]);
    }

    public function test_destroy_no_permitido_para_usuarios_user()
    {
        $user = $this->createUser();
        $actor = $this->createActor();

        $response = $this->actingAs($user)->delete(route('actores.destroy', $actor->id));

        $response->assertStatus(403);
        $this->assertDatabaseHas('actores', ['id' => $actor->id, 'deleted_at' => null]);
    }

    public function test_destroy_no_permitido_para_usuarios_no_autenticados()
    {
        $actor = $this->createActor();

        $response = $this->delete(route('actores.destroy', $actor->id));

        $response->assertRedirect(route('login'));
        $this->assertDatabaseHas('actores', ['id' => $actor->id, 'deleted_at' => null]);
    }

    public function test_deleted_muestra_actores_eliminados_para_usuarios_admin()
    {
        $admin = $this->createAdminUser();
        $actoresActivos = $this->createActores(2);
        $actoresEliminados = $this->createActores(2);

        $actoresEliminados->each->delete();

        $responseDeleted = $this->actingAs($admin)->get(route('actores.deleted'));


        $responseDeleted->assertStatus(200)
            ->assertViewIs('actores.deleted')
            ->assertViewHas('actores', function ($viewActores) use ($actoresEliminados) {
                return $viewActores->pluck('id')->diff($actoresEliminados->pluck('id'))->isEmpty();
            });


        $responseDeleted->assertViewHas('actores', function ($viewActores) use ($actoresActivos) {
            return $viewActores->pluck('id')->intersect($actoresActivos->pluck('id'))->isEmpty();
        });


        $responseIndex = $this->actingAs($admin)->get(route('actores.index'));

        $responseIndex->assertStatus(200)
            ->assertViewIs('actores.index')
            ->assertViewHas('actores', function ($viewActores) use ($actoresActivos) {
                return $viewActores->pluck('id')->diff($actoresActivos->pluck('id'))->isEmpty();
            });


        $responseIndex->assertViewHas('actores', function ($viewActores) use ($actoresEliminados) {
            return $viewActores->pluck('id')->intersect($actoresEliminados->pluck('id'))->isEmpty();
        });
    }

    public function test_deleted_no_permitido_para_usuarios_user()
    {
        $user = $this->createUser();

        $response = $this->actingAs($user)->get(route('actores.deleted'));

        $response->assertStatus(403);
    }

    public function test_deleted_no_permitido_para_usuarios_no_autenticados()
    {
        $response = $this->get(route('actores.deleted'));

        $response->assertRedirect(route('login'));
    }

    public function test_restore_restaurar_actor_para_usuarios_admin()
    {
        $admin = $this->createAdminUser();
        $actor = $this->createActor();
        $actor->delete();

        $response = $this->actingAs($admin)->post(route('actores.restore', $actor->id));

        $response->assertRedirect(route('actores.deleted'));
        $this->assertDatabaseHas('actores', [
            'id' => $actor->id,
            'deleted_at' => null,
        ]);
    }

    public function test_restore_no_permitido_para_usuarios_user()
    {
        $user = $this->createUser();
        $actor = $this->createActor();
        $actor->delete();

        $response = $this->actingAs($user)->post(route('actores.restore', $actor->id));

        $response->assertStatus(403);
        $this->assertSoftDeleted('actores', ['id' => $actor->id]);
    }

    public function test_restore_no_permitido_para_usuarios_no_autenticados()
    {
        $actor = $this->createActor();
        $actor->delete();

        $response = $this->post(route('actores.restore', $actor->id));

        $response->assertRedirect(route('login'));
        $this->assertSoftDeleted('actores', ['id' => $actor->id]);
    }

}
