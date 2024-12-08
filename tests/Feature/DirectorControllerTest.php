<?php

namespace Tests\Feature;

use App\Models\Director;
use App\Models\Pelicula;
use App\Models\Premio;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class DirectorControllerTest extends TestCase
{
    use RefreshDatabase;

    private function createDirector($attributes = [])
    {
        return Director::factory()->create($attributes);
    }

    private function createDirectores($count = 1, $attributes = [])
    {
        return Director::factory()->count($count)->create($attributes);
    }

    private function createPeliculas($count = 1, $attributes = [])
    {
        return Pelicula::factory()->count($count)->create($attributes);
    }

    private function createPremios($count = 1, $attributes = [])
    {
        $defaultAttributes = [
            'entidad_type' => Director::class,
            'entidad_id' => $attributes['director_id'] ?? $this->createDirector()->id,
        ];

        unset($attributes['director_id']);

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


    public function test_index_devuelve_vista_con_directores()
    {
        $directores = $this->createDirectores(5);

        $response = $this->get(route('directores.index'));

        $response->assertStatus(200)
            ->assertViewIs('directores.index')
            ->assertViewHas('directores', function ($viewDirectores) use ($directores) {
                return $viewDirectores->pluck('id')->diff($directores->pluck('id'))->isEmpty();
            });
    }

    public function test_show_devuelve_vista_con_director_y_sus_peliculas_y_premios()
    {
        $director = $this->createDirector();
        $peliculas = $this->createPeliculas(3, ['director_id' => $director->id]);
        $premios = $this->createPremios(2, ['entidad_id' => $director->id, 'entidad_type' => Director::class]);

        $response = $this->get(route('directores.show', $director->id));

        $response->assertStatus(200)
            ->assertViewIs('directores.show')
            ->assertViewHas('director', fn($viewDirector) => $viewDirector->id === $director->id)
            ->assertViewHas('peliculas', function ($viewPeliculas) use ($peliculas) {
                return $viewPeliculas->pluck('id')->diff($peliculas->pluck('id'))->isEmpty();
            });


        $this->assertTrue(
            $director->premios->pluck('id')->diff($premios->pluck('id'))->isEmpty(),
            'Los premios no están asociados correctamente al director.'
        );
    }

    public function test_create_devuelve_vista_para_usuarios_admin()
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)->get(route('directores.create'));

        $response->assertStatus(200)
            ->assertViewIs('directores.create')
            ->assertViewHas('director', fn($viewDirector) => $viewDirector instanceof \App\Models\Director);
    }

    public function test_create_no_permitido_para_usuarios_no_admin()
    {
        $user = $this->createUser();

        $response = $this->actingAs($user)->get(route('directores.create'));

        $response->assertStatus(403);
    }

    public function test_create_no_permitido_para_usuarios_no_autenticados()
    {
        $response = $this->get(route('directores.create'));

        $response->assertRedirect(route('login'));
    }

    public function test_store_crea_director_para_usuarios_admin()
    {
        $admin = $this->createAdminUser();

        $data = [
            'nombre' => 'Steven Spielberg',
            'fecha_nac' => '1946-12-18',
            'lugar_nac' => 'Cincinnati, Ohio, USA',
            'biografia' => 'Un director icónico.',
            'inicio_actividad' => 1971,
            'fin_actividad' => null,
            'activo' => true,
        ];

        $response = $this->actingAs($admin)->post(route('directores.store'), $data);

        $response->assertRedirect(route('directores.show', Director::latest('id')->first()->id));

        $this->assertDatabaseHas('directores', [
            'nombre' => 'Steven Spielberg',
            'fecha_nac' => '1946-12-18',
            'lugar_nac' => 'Cincinnati, Ohio, USA',
            'biografia' => 'Un director icónico.',
            'inicio_actividad' => 1971,
            'activo' => true,
        ]);
    }

    public function test_store_no_crea_director_con_datos_invalidos()
    {
        $admin = $this->createAdminUser();

        $data = [
            'nombre' => '',
            'fecha_nac' => 'invalid-date',
        ];

        $response = $this->actingAs($admin)->post(route('directores.store'), $data);

        $response->assertSessionHasErrors(['nombre', 'fecha_nac']);
        $this->assertDatabaseMissing('directores', ['nombre' => '']);
    }

    public function test_store_no_crea_director_con_fechas_incoherentes()
    {
        $admin = $this->createAdminUser();

        $data = [
            'nombre' => 'James Cameron',
            'fecha_nac' => '1960-01-01',
            'inicio_actividad' => 1950,
        ];

        $response = $this->actingAs($admin)->post(route('directores.store'), $data);

        $response->assertSessionHasErrors(['inicio_actividad']);
        $this->assertDatabaseMissing('directores', ['nombre' => 'James Cameron']);
    }


    public function test_store_no_permitido_para_usuarios_no_admin()
    {
        $user = $this->createUser();

        $data = [
            'nombre' => 'Steven Spielberg',
            'fecha_nac' => '1946-12-18',
        ];

        $response = $this->actingAs($user)->post(route('directores.store'), $data);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('directores', ['nombre' => 'Steven Spielberg']);
    }

    public function test_store_no_permitido_para_usuarios_no_autenticados()
    {
        $data = [
            'nombre' => 'Steven Spielberg',
            'fecha_nac' => '1946-12-18',
        ];

        $response = $this->post(route('directores.store'), $data);

        $response->assertRedirect(route('login'));
        $this->assertDatabaseMissing('directores', ['nombre' => 'Steven Spielberg']);
    }

    public function test_edit_devuelve_vista_para_usuarios_admin()
    {
        $admin = $this->createAdminUser();
        $director = $this->createDirector();

        $response = $this->actingAs($admin)->get(route('directores.edit', $director->id));

        $response->assertStatus(200)
            ->assertViewIs('directores.edit')
            ->assertViewHas('director', fn($viewDirector) => $viewDirector->id === $director->id);
    }




    public function test_edit_no_permitido_para_usuarios_no_admin()
    {
        $user = $this->createUser();
        $director = $this->createDirector();

        $response = $this->actingAs($user)->get(route('directores.edit', $director->id));

        $response->assertStatus(403);
    }

    public function test_edit_no_permitido_para_usuarios_no_autenticados()
    {
        $director = $this->createDirector();

        $response = $this->get(route('directores.edit', $director->id));

        $response->assertRedirect(route('login'));
    }

    public function test_update_actualiza_director_para_usuarios_admin()
    {
        $admin = $this->createAdminUser();
        $director = $this->createDirector([
            'nombre' => 'Quentin Tarantino',
            'inicio_actividad' => 1998,
        ]);

        $peliculas = $this->createPeliculas(3, ['director_id' => $director->id]);

        $data = [
            'nombre' => 'Christopher Nolan',
            'fecha_nac' => '1970-07-30',
            'lugar_nac' => 'Londres, Reino Unido',
            'biografia' => 'Un director reconocido.',
            'inicio_actividad' => 1998,
            'fin_actividad' => null,
            'activo' => true,
        ];

        $response = $this->actingAs($admin)->patch(route('directores.update', $director->id), $data);

        $response->assertRedirect(route('directores.show', $director->id));


        $this->assertDatabaseHas('directores', [
            'id' => $director->id,
            'nombre' => 'Christopher Nolan',
            'fecha_nac' => '1970-07-30',
            'lugar_nac' => 'Londres, Reino Unido',
            'inicio_actividad' => 1998,
            'activo' => true,
        ]);


        $director->refresh();
        $this->assertTrue(
            $director->peliculas->pluck('id')->diff($peliculas->pluck('id'))->isEmpty(),
            'Las películas no están correctamente relacionadas con el director.'
        );
    }



    public function test_update_no_actualiza_director_con_datos_invalidos()
    {
        $admin = $this->createAdminUser();
        $director = $this->createDirector(['nombre' => 'Quentin Tarantino']);

        $data = [
            'nombre' => '',
            'fecha_nac' => 'invalid-date',
        ];

        $response = $this->actingAs($admin)->patch(route('directores.update', $director->id), $data);

        $response->assertSessionHasErrors(['nombre', 'fecha_nac']);
        $this->assertDatabaseHas('directores', ['id' => $director->id, 'nombre' => 'Quentin Tarantino']);
    }

    public function test_update_no_permitido_para_usuarios_no_admin()
    {
        $user = $this->createUser();
        $director = $this->createDirector();

        $data = [
            'nombre' => 'Christopher Nolan',
        ];

        $response = $this->actingAs($user)->patch(route('directores.update', $director->id), $data);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('directores', ['nombre' => 'Christopher Nolan']);
    }

    public function test_update_no_permitido_para_usuarios_no_autenticados()
    {
        $director = $this->createDirector();

        $data = [
            'nombre' => 'Christopher Nolan',
        ];

        $response = $this->patch(route('directores.update', $director->id), $data);

        $response->assertRedirect(route('login'));
        $this->assertDatabaseMissing('directores', ['nombre' => 'Christopher Nolan']);
    }

    public function test_destroy_elimina_director_para_usuarios_admin()
    {
        $admin = $this->createAdminUser();
        $director = $this->createDirector();

        $response = $this->actingAs($admin)->delete(route('directores.destroy', $director->id));

        $response->assertRedirect(route('admin.directores'));
        $this->assertSoftDeleted('directores', ['id' => $director->id]);
    }

    public function test_destroy_no_permitido_para_usuarios_user()
    {
        $user = $this->createUser();
        $director = $this->createDirector();

        $response = $this->actingAs($user)->delete(route('directores.destroy', $director->id));

        $response->assertStatus(403);
        $this->assertDatabaseHas('directores', ['id' => $director->id, 'deleted_at' => null]);
    }

    public function test_destroy_no_permitido_para_usuarios_no_autenticados()
    {
        $director = $this->createDirector();

        $response = $this->delete(route('directores.destroy', $director->id));

        $response->assertRedirect(route('login'));
        $this->assertDatabaseHas('directores', ['id' => $director->id, 'deleted_at' => null]);
    }

    public function test_deleted_muestra_directores_eliminados_para_usuarios_admin()
    {
        $admin = $this->createAdminUser();
        $directoresActivos = $this->createDirectores(2);
        $directoresEliminados = $this->createDirectores(2);


        $directoresEliminados->each->delete();


        $responseDeleted = $this->actingAs($admin)->get(route('directores.deleted'));

        $responseDeleted->assertStatus(200)
            ->assertViewIs('directores.deleted')
            ->assertViewHas('directores', function ($viewDirectores) use ($directoresEliminados) {
                return $viewDirectores->pluck('id')->diff($directoresEliminados->pluck('id'))->isEmpty();
            });


        $responseDeleted->assertViewHas('directores', function ($viewDirectores) use ($directoresActivos) {
            return $viewDirectores->pluck('id')->intersect($directoresActivos->pluck('id'))->isEmpty();
        });


        $responseIndex = $this->actingAs($admin)->get(route('directores.index'));

        $responseIndex->assertStatus(200)
            ->assertViewIs('directores.index')
            ->assertViewHas('directores', function ($viewDirectores) use ($directoresActivos) {
                return $viewDirectores->pluck('id')->diff($directoresActivos->pluck('id'))->isEmpty();
            });


        $responseIndex->assertViewHas('directores', function ($viewDirectores) use ($directoresEliminados) {
            return $viewDirectores->pluck('id')->intersect($directoresEliminados->pluck('id'))->isEmpty();
        });
    }

    public function test_deleted_no_permitido_para_usuarios_user()
    {
        $user = $this->createUser();

        $response = $this->actingAs($user)->get(route('directores.deleted'));

        $response->assertStatus(403);
    }

    public function test_deleted_no_permitido_para_usuarios_no_autenticados()
    {
        $response = $this->get(route('directores.deleted'));

        $response->assertRedirect(route('login'));
    }

    public function test_restore_restaurar_director_para_usuarios_admin()
    {
        $admin = $this->createAdminUser();
        $director = $this->createDirector();
        $director->delete();

        $response = $this->actingAs($admin)->post(route('directores.restore', $director->id));

        $response->assertRedirect(route('directores.deleted'));
        $this->assertDatabaseHas('directores', [
            'id' => $director->id,
            'deleted_at' => null,
        ]);
    }

    public function test_restore_no_permitido_para_usuarios_user()
    {
        $user = $this->createUser();
        $director = $this->createDirector();
        $director->delete();

        $response = $this->actingAs($user)->post(route('directores.restore', $director->id));

        $response->assertStatus(403);
        $this->assertSoftDeleted('directores', ['id' => $director->id]);
    }

    public function test_restore_no_permitido_para_usuarios_no_autenticados()
    {
        $director = $this->createDirector();
        $director->delete();

        $response = $this->post(route('directores.restore', $director->id));

        $response->assertRedirect(route('login'));
        $this->assertSoftDeleted('directores', ['id' => $director->id]);
    }


}
