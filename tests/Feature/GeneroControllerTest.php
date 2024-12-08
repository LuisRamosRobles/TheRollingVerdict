<?php

namespace Tests\Feature;


use App\Models\Genero;
use App\Models\Pelicula;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GeneroControllerTest extends TestCase
{
    use RefreshDatabase;

    private function createGeneros($count = 1, $attributes = [])
    {
        return Genero::factory()->count($count)->create($attributes);
    }

    private function createGenero($attributes = [])
    {
        return Genero::factory()->create($attributes);
    }

    private function createPeliculas($count = 1, $attributes = [])
    {
        return Pelicula::factory()->count($count)->create($attributes);
    }

    private function createAdminUser()
    {
        return User::factory()->create(['role' => 'ADMIN']);
    }

    private function createUser()
    {
        return User::factory()->create(['role' => 'USER']);
    }

    public function test_index_devuelve_vista_con_generos()
    {
        $generos = $this->createGeneros(5);

        $response = $this->get(route('generos.index'));

        $response->assertStatus(200)
            ->assertViewIs('generos.index')
            ->assertViewHas('generos', function ($viewGeneros) use ($generos) {
                return $viewGeneros->pluck('id')->diff($generos->pluck('id'))->isEmpty();
            });
    }

    public function test_show_devuelve_vista_con_genero_y_peliculas()
    {
        $genero = $this->createGeneros()->first();
        $peliculas = $this->createPeliculas(3);

        $genero->peliculas()->sync($peliculas->pluck('id')->toArray());

        $response = $this->get(route('generos.show', $genero->id));

        $response->assertStatus(200)
            ->assertViewIs('generos.show')
            ->assertViewHas('genero', fn($viewGenero) => $viewGenero->id === $genero->id)
            ->assertViewHas('peliculas', function ($viewPeliculas) use ($peliculas) {
                return $viewPeliculas->pluck('id')->diff($peliculas->pluck('id'))->isEmpty();
            });
    }

    public function test_create_devuelve_vista_para_usuarios_admin()
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)->get(route('generos.create'));

        $response->assertStatus(200)
            ->assertViewIs('generos.create');
    }

    public function test_create_no_permitido_para_usuarios_no_admin()
    {
        $user = $this->createUser();

        $response = $this->actingAs($user)->get(route('generos.create'));

        $response->assertStatus(403);
    }

    public function test_create_no_permitido_para_usuarios_no_autenticados()
    {
        $response = $this->get(route('generos.create'));

        $response->assertRedirect(route('login'));
    }

    public function test_store_crea_genero_para_usuarios_admin()
    {
        $admin = $this->createAdminUser();

        $data = [
            'nombre' => 'Acción',
        ];

        $response = $this->actingAs($admin)->post(route('generos.store'), $data);

        $response->assertRedirect(route('generos.show', Genero::latest('id')->first()->id));

        $this->assertDatabaseHas('generos', [
            'nombre' => 'Acción',
        ]);
    }

    public function test_store_no_crea_genero_con_datos_invalidos()
    {
        $admin = $this->createAdminUser();

        $data = [
            'nombre' => '',
        ];

        $response = $this->actingAs($admin)->post(route('generos.store'), $data);

        $response->assertSessionHasErrors(['nombre']);
        $this->assertDatabaseMissing('generos', ['nombre' => '']);
    }

    public function test_store_no_crea_genero_con_nombre_duplicado()
    {
        $admin = $this->createAdminUser();

        Genero::factory()->create(['nombre' => 'Acción']);

        $data = [
            'nombre' => 'Acción',
        ];

        $response = $this->actingAs($admin)->post(route('generos.store'), $data);

        $response->assertSessionHasErrors(['nombre']);
        $this->assertEquals(1, Genero::where('nombre', 'Acción')->count());
    }

    public function test_store_no_crea_genero_para_usuarios_no_admin()
    {
        $user = $this->createUser();

        $data = [
            'nombre' => 'Acción',
        ];

        $response = $this->actingAs($user)->post(route('generos.store'), $data);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('generos', ['nombre' => 'Acción']);
    }

    public function test_store_no_crea_genero_para_usuarios_no_autenticados()
    {
        $data = [
            'nombre' => 'Acción',
        ];

        $response = $this->post(route('generos.store'), $data);

        $response->assertRedirect(route('login'));
        $this->assertDatabaseMissing('generos', ['nombre' => 'Acción']);
    }

    public function test_edit_devuelve_vista_para_usuarios_admin()
    {
        $admin = $this->createAdminUser();
        $genero = $this->createGenero()->first();

        $response = $this->actingAs($admin)->get(route('generos.edit', $genero->id));

        $response->assertStatus(200)
            ->assertViewIs('generos.edit')
            ->assertViewHas('genero', fn($viewGenero) => $viewGenero->id === $genero->id);
    }

    public function test_edit_no_permitido_para_usuarios_no_admin()
    {
        $user = $this->createUser();
        $genero = $this->createGenero()->first();

        $response = $this->actingAs($user)->get(route('generos.edit', $genero->id));

        $response->assertStatus(403);
    }

    public function test_edit_no_permitido_para_usuarios_no_autenticados()
    {
        $genero = $this->createGenero()->first();

        $response = $this->get(route('generos.edit', $genero->id));

        $response->assertRedirect(route('login'));
    }

    public function test_update_actualiza_genero_para_usuarios_admin()
    {
        $admin = $this->createAdminUser();
        $genero = $this->createGenero(['nombre' => 'Acción']);

        $data = [
            'nombre' => 'Aventura',
        ];

        $response = $this->actingAs($admin)->patch(route('generos.update', $genero->id), $data);

        $response->assertRedirect(route('generos.show', $genero->id));

        $this->assertDatabaseHas('generos', [
            'id' => $genero->id,
            'nombre' => 'Aventura',
        ]);
    }

    public function test_update_no_actualiza_genero_con_datos_invalidos()
    {
        $admin = $this->createAdminUser();
        $genero = $this->createGenero(['nombre' => 'Acción']);

        $data = [
            'nombre' => '',
        ];

        $response = $this->actingAs($admin)->patch(route('generos.update', $genero->id), $data);

        $response->assertSessionHasErrors(['nombre']);
        $this->assertDatabaseHas('generos', ['id' => $genero->id, 'nombre' => 'Acción']);
    }

    public function test_update_no_actualiza_genero_con_nombre_duplicado()
    {
        $admin = $this->createAdminUser();
        $this->createGenero(['nombre' => 'Acción']);
        $genero = $this->createGenero(['nombre' => 'Aventura']);

        $data = [
            'nombre' => 'Acción',
        ];

        $response = $this->actingAs($admin)->patch(route('generos.update', $genero->id), $data);

        $response->assertSessionHasErrors(['nombre']);
        $this->assertDatabaseHas('generos', ['id' => $genero->id, 'nombre' => 'Aventura']);
    }

    public function test_update_no_permitido_para_usuarios_no_admin()
    {
        $user = $this->createUser();
        $genero = $this->createGenero(['nombre' => 'Acción']);

        $data = [
            'nombre' => 'Aventura',
        ];

        $response = $this->actingAs($user)->patch(route('generos.update', $genero->id), $data);

        $response->assertStatus(403);
        $this->assertDatabaseHas('generos', ['id' => $genero->id, 'nombre' => 'Acción']);
    }

    public function test_update_no_permitido_para_usuarios_no_autenticados()
    {
        $genero = $this->createGenero(['nombre' => 'Acción']);

        $data = [
            'nombre' => 'Aventura',
        ];

        $response = $this->patch(route('generos.update', $genero->id), $data);

        $response->assertRedirect(route('login'));
        $this->assertDatabaseHas('generos', ['id' => $genero->id, 'nombre' => 'Acción']);
    }

    public function test_destroy_elimina_genero_para_usuarios_admin()
    {
        $admin = $this->createAdminUser();
        $genero = $this->createGenero();

        $response = $this->actingAs($admin)->delete(route('generos.destroy', $genero->id));

        $response->assertRedirect(route('admin.generos'));
        $this->assertSoftDeleted('generos', ['id' => $genero->id]);
    }

    public function test_destroy_no_permitido_para_usuarios_user()
    {
        $user = $this->createUser();
        $genero = $this->createGenero();

        $response = $this->actingAs($user)->delete(route('generos.destroy', $genero->id));

        $response->assertStatus(403);
        $this->assertDatabaseHas('generos', ['id' => $genero->id, 'deleted_at' => null]);
    }

    public function test_destroy_no_permitido_para_usuarios_no_autenticados()
    {
        $genero = $this->createGenero();

        $response = $this->delete(route('generos.destroy', $genero->id));

        $response->assertRedirect(route('login'));
        $this->assertDatabaseHas('generos', ['id' => $genero->id, 'deleted_at' => null]);
    }

    public function test_deleted_muestra_generos_eliminados_para_usuarios_admin()
    {
        $admin = $this->createAdminUser();
        $generosActivos = $this->createGeneros(2);
        $generosEliminados = $this->createGeneros(2);


        $generosEliminados->each->delete();

        $response = $this->actingAs($admin)->get(route('generos.deleted'));

        $response->assertStatus(200)
            ->assertViewIs('generos.deleted')
            ->assertViewHas('generos', function ($generos) use ($generosEliminados, $generosActivos) {

                $eliminadosPresentes = $generos->pluck('id')->diff($generosEliminados->pluck('id'))->isEmpty();


                $activosAusentes = $generos->pluck('id')->intersect($generosActivos->pluck('id'))->isEmpty();

                return $eliminadosPresentes && $activosAusentes;
            });
    }


    public function test_deleted_no_permitido_para_usuarios_user()
    {
        $user = $this->createUser();

        $response = $this->actingAs($user)->get(route('generos.deleted'));

        $response->assertStatus(403);
    }

    public function test_deleted_no_permitido_para_usuarios_no_autenticados()
    {
        $response = $this->get(route('generos.deleted'));

        $response->assertRedirect(route('login'));
    }

    public function test_restore_restaurar_genero_eliminado_para_usuarios_admin()
    {
        $admin = $this->createAdminUser();
        $genero = $this->createGenero();
        $genero->delete();

        $response = $this->actingAs($admin)->post(route('generos.restore', $genero->id));

        $response->assertRedirect(route('generos.deleted'));

        $this->assertDatabaseHas('generos', [
            'id' => $genero->id,
            'deleted_at' => null,
        ]);
    }

    public function test_restore_no_permitido_para_usuarios_user()
    {
        $user = $this->createUser();
        $genero = $this->createGenero();
        $genero->delete();

        $response = $this->actingAs($user)->post(route('generos.restore', $genero->id));

        $response->assertStatus(403);
        $this->assertSoftDeleted('generos', ['id' => $genero->id]);
    }

    public function test_restore_no_permitido_para_usuarios_no_autenticados()
    {
        $genero = $this->createGenero();
        $genero->delete();

        $response = $this->post(route('generos.restore', $genero->id));

        $response->assertRedirect(route('login'));
        $this->assertSoftDeleted('generos', ['id' => $genero->id]);
    }


}
