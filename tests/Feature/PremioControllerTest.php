<?php

namespace Tests\Feature;

use App\Models\Actor;
use App\Models\Director;
use App\Models\Pelicula;
use App\Models\Premio;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PremioControllerTest extends TestCase
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


    private function createPremios($count = 1, $attributes = [])
    {
        return Premio::factory()->count($count)->create($attributes);
    }

    private function createPremio($attributes = [])
    {
        return Premio::factory()->create($attributes);
    }

    private function createPelicula($attributes = [])
    {
        return Pelicula::factory()->create($attributes);
    }

    private function createDirector($attributes = [])
    {
        return Director::factory()->create($attributes);
    }

    private function createActor($attributes = [])
    {
        return Actor::factory()->create($attributes);
    }

    public function test_index_devuelve_vista_con_premios()
    {
        $premios = $this->createPremios(5);

        $response = $this->get(route('premios.index'));

        $response->assertStatus(200)
            ->assertViewIs('premios.index')
            ->assertViewHas('premios', function ($viewPremios) use ($premios) {
                return $viewPremios->pluck('id')->diff($premios->pluck('id'))->isEmpty();
            });
    }

    public function test_index_aplica_orden_correcto()
    {
        $premios = $this->createPremios(5, [
            'anio' => now()->year,
        ])->sortBy(['anio' => 'desc', 'nombre' => 'asc']);

        $response = $this->get(route('premios.index'));

        $response->assertStatus(200)
            ->assertViewHas('premios', function ($viewPremios) use ($premios) {
                return $viewPremios->pluck('id')->diff($premios->pluck('id'))->isEmpty();
            });
    }

    public function test_show_devuelve_vista_con_premio()
    {
        $premio = $this->createPremios(1)->first();

        $response = $this->get(route('premios.show', $premio->id));

        $response->assertStatus(200)
            ->assertViewIs('premios.show')
            ->assertViewHas('premio', fn($viewPremio) => $viewPremio->id === $premio->id);
    }

    public function test_show_lanza_error_si_premio_no_existe()
    {
        $response = $this->get(route('premios.show', 999));

        $response->assertStatus(404);
    }

    public function test_create_devuelve_vista_para_usuarios_admin()
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)->get(route('premios.create'));

        $response->assertStatus(200)
            ->assertViewIs('premios.create')
            ->assertViewHasAll(['premio', 'peliculas', 'directores', 'actores']);
    }

    public function test_create_no_permitido_para_usuarios_no_admin()
    {
        $user = $this->createUser();

        $response = $this->actingAs($user)->get(route('premios.create'));

        $response->assertStatus(403);
    }

    public function test_create_no_permitido_para_usuarios_no_autenticados()
    {
        $response = $this->get(route('premios.create'));

        $response->assertRedirect(route('login'));
    }

    public function test_store_crea_premio_para_usuarios_admin()
    {
        $admin = $this->createAdminUser();
        $pelicula = $this->createPelicula(['estreno' => '2020-01-01']);
        $anioPremio = 2021;

        $data = [
            'nombre' => 'Oscar',
            'categoria' => 'Mejor Película',
            'anio' => $anioPremio,
            'entidad_type' => Pelicula::class,
            'entidad_id' => $pelicula->id,
            'pelicula_id' => $pelicula->id,
        ];

        $response = $this->actingAs($admin)->post(route('premios.store'), $data);

        $response->assertRedirect(route('premios.show', Premio::latest('id')->first()->id));
        $this->assertDatabaseHas('premios', [
            'nombre' => 'Oscar',
            'categoria' => 'Mejor Película',
            'anio' => $anioPremio,
            'entidad_type' => Pelicula::class,
            'entidad_id' => $pelicula->id,
        ]);
    }

    public function test_store_no_crea_premio_con_datos_invalidos()
    {
        $admin = $this->createAdminUser();

        $data = [
            'nombre' => 'Oscar',
            'categoria' => 'Prueba',
            'anio' => '2015',
            'entidad_type' => 'App\Models\Pelicula',
            'entidad_id' => 999,
        ];

        $response = $this->actingAs($admin)->post(route('premios.store'), $data);

        $response->assertSessionHasErrors([
            'entidad_id',
        ]);

        $this->assertDatabaseMissing('premios', ['nombre' => 'Oscar']);
    }

    public function test_store_no_crea_premio_para_usuarios_no_admin()
    {
        $user = $this->createUser();
        $pelicula = $this->createPelicula();

        $data = [
            'nombre' => 'Oscar',
            'categoria' => 'Mejor Película',
            'anio' => 2021,
            'entidad_type' => Pelicula::class,
            'entidad_id' => $pelicula->id,
        ];

        $response = $this->actingAs($user)->post(route('premios.store'), $data);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('premios', ['nombre' => 'Oscar']);
    }

    public function test_store_no_crea_premio_para_usuarios_no_autenticados()
    {
        $pelicula = $this->createPelicula();

        $data = [
            'nombre' => 'Oscar',
            'categoria' => 'Mejor Película',
            'anio' => 2021,
            'entidad_type' => Pelicula::class,
            'entidad_id' => $pelicula->id,
        ];

        $response = $this->post(route('premios.store'), $data);

        $response->assertRedirect(route('login'));
        $this->assertDatabaseMissing('premios', ['nombre' => 'Oscar']);
    }

    public function test_edit_devuelve_vista_para_usuarios_admin()
    {
        $admin = $this->createAdminUser();
        $premio = Premio::factory()->create();

        $response = $this->actingAs($admin)->get(route('premios.edit', $premio->id));

        $response->assertStatus(200)
            ->assertViewIs('premios.edit')
            ->assertViewHas('premio', fn($viewPremio) => $viewPremio->id === $premio->id);
    }

    public function test_edit_no_permitido_para_usuarios_no_admin()
    {
        $user = $this->createUser();
        $premio = Premio::factory()->create();

        $response = $this->actingAs($user)->get(route('premios.edit', $premio->id));

        $response->assertStatus(403);
    }

    public function test_edit_no_permitido_para_usuarios_no_autenticados()
    {
        $premio = Premio::factory()->create();

        $response = $this->get(route('premios.edit', $premio->id));

        $response->assertRedirect(route('login'));
    }

    public function test_update_actualiza_premio_para_usuarios_admin()
    {
        $admin = $this->createAdminUser();
        $premio = Premio::factory()->create([
            'nombre' => 'Oscar',
            'categoria' => 'Mejor Película',
            'anio' => 2020,
        ]);
        $pelicula = Pelicula::factory()->create(['estreno' => '2020-01-01']);
        $data = [
            'nombre' => 'Golden Globe',
            'categoria' => 'Mejor Película',
            'anio' => 2021,
            'entidad_type' => Pelicula::class,
            'entidad_id' => $pelicula->id,
            'pelicula_id' => $pelicula->id,
        ];

        $response = $this->actingAs($admin)->patch(route('premios.update', $premio->id), $data);

        $response->assertRedirect(route('premios.show', $premio->id));

        $this->assertDatabaseHas('premios', [
            'id' => $premio->id,
            'nombre' => 'Golden Globe',
            'categoria' => 'Mejor Película',
            'anio' => 2021,
        ]);
    }

    public function test_update_no_actualiza_premio_con_datos_invalidos()
    {
        $admin = $this->createAdminUser();
        $premio = $this->createPremio([
            'nombre' => 'Oscar',
            'categoria' => 'Prueba',
            'anio' => 2023,
            'entidad_type' => Pelicula::class,
            'entidad_id' => $this->createPelicula()->id,
        ]);

        $data = [
            'nombre' => '',
            'categoria' => '',
            'anio' => 'invalid-date',
            'entidad_type' => 'InvalidType',
            'entidad_id' => 999,
        ];

        $response = $this->actingAs($admin)->patch(route('premios.update', $premio->id), $data);

        $response->assertSessionHasErrors([
            'nombre',
            'categoria',
            'anio',
            'entidad_type',
            'entidad_id',
        ]);

        $this->assertDatabaseHas('premios', [
            'id' => $premio->id,
            'nombre' => 'Oscar',
        ]);
    }

    public function test_update_no_permitido_para_usuarios_no_admin()
    {
        $user = $this->createUser();
        $premio = $this->createPremio();

        $data = [
            'nombre' => 'Golden Globe',
            'categoria' => 'Mejor Película',
            'anio' => 2021,
        ];

        $response = $this->actingAs($user)->patch(route('premios.update', $premio->id), $data);

        $response->assertStatus(403);
        $this->assertDatabaseHas('premios', ['id' => $premio->id]);
    }

    public function test_update_no_permitido_para_usuarios_no_autenticados()
    {
        $premio = Premio::factory()->create();

        $data = [
            'nombre' => 'Golden Globe',
            'categoria' => 'Mejor Película',
            'anio' => 2021,
        ];

        $response = $this->patch(route('premios.update', $premio->id), $data);

        $response->assertRedirect(route('login'));
        $this->assertDatabaseHas('premios', ['id' => $premio->id]);
    }

    public function test_destroy_elimina_premio_para_usuarios_admin()
    {
        $admin = $this->createAdminUser();
        $premio = $this->createPremio();

        $response = $this->actingAs($admin)->delete(route('premios.destroy', $premio->id));

        $response->assertRedirect(route('admin.premios'));
        $this->assertSoftDeleted('premios', ['id' => $premio->id]);
    }

    public function test_destroy_no_permitido_para_usuarios_no_admin()
    {
        $user = $this->createUser();
        $premio = $this->createPremio();

        $response = $this->actingAs($user)->delete(route('premios.destroy', $premio->id));

        $response->assertStatus(403);
        $this->assertDatabaseHas('premios', ['id' => $premio->id, 'deleted_at' => null]);
    }

    public function test_destroy_no_permitido_para_usuarios_no_autenticados()
    {
        $premio = $this->createPremio();

        $response = $this->delete(route('premios.destroy', $premio->id));

        $response->assertRedirect(route('login'));
        $this->assertDatabaseHas('premios', ['id' => $premio->id, 'deleted_at' => null]);
    }

    public function test_deleted_muestra_premios_eliminados_para_usuarios_admin()
    {
        $admin = $this->createAdminUser();


        $premiosActivos = $this->createPremios(2);
        $premiosEliminados = $this->createPremios(2);


        $premiosEliminados->each->delete();


        $responseDeleted = $this->actingAs($admin)->get(route('premios.deleted'));
        $responseDeleted->assertStatus(200)
            ->assertViewIs('premios.deleted')
            ->assertViewHas('premios', function ($viewPremios) use ($premiosEliminados) {
                return $viewPremios->pluck('id')->diff($premiosEliminados->pluck('id'))->isEmpty();
            });


        $responseDeleted->assertViewHas('premios', function ($viewPremios) use ($premiosActivos) {
            return $viewPremios->pluck('id')->intersect($premiosActivos->pluck('id'))->isEmpty();
        });


        $responseIndex = $this->actingAs($admin)->get(route('premios.index'));
        $responseIndex->assertStatus(200)
            ->assertViewIs('premios.index')
            ->assertViewHas('premios', function ($viewPremios) use ($premiosActivos) {
                return $viewPremios->pluck('id')->diff($premiosActivos->pluck('id'))->isEmpty();
            });


        $responseIndex->assertViewHas('premios', function ($viewPremios) use ($premiosEliminados) {
            return $viewPremios->pluck('id')->intersect($premiosEliminados->pluck('id'))->isEmpty();
        });
    }

    public function test_deleted_no_permitido_para_usuarios_no_admin()
    {
        $user = $this->createUser();

        $response = $this->actingAs($user)->get(route('premios.deleted'));

        $response->assertStatus(403);
    }

    public function test_deleted_no_permitido_para_usuarios_no_autenticados()
    {
        $response = $this->get(route('premios.deleted'));

        $response->assertRedirect(route('login'));
    }

    public function test_restore_restaurar_premio_para_usuarios_admin()
    {
        $admin = $this->createAdminUser();
        $premio = $this->createPremio();
        $premio->delete();

        $response = $this->actingAs($admin)->post(route('premios.restore', $premio->id));

        $response->assertRedirect(route('premios.deleted'));
        $this->assertDatabaseHas('premios', [
            'id' => $premio->id,
            'deleted_at' => null,
        ]);
    }

    public function test_restore_no_permitido_para_usuarios_no_admin()
    {
        $user = $this->createUser();
        $premio = $this->createPremio();
        $premio->delete();

        $response = $this->actingAs($user)->post(route('premios.restore', $premio->id));

        $response->assertStatus(403);
        $this->assertSoftDeleted('premios', ['id' => $premio->id]);
    }

    public function test_restore_no_permitido_para_usuarios_no_autenticados()
    {
        $premio = $this->createPremio();
        $premio->delete();

        $response = $this->post(route('premios.restore', $premio->id));

        $response->assertRedirect(route('login'));
        $this->assertSoftDeleted('premios', ['id' => $premio->id]);
    }


}
