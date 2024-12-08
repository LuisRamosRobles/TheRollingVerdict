<?php

namespace Tests\Feature;

use App\Models\Pelicula;
use App\Models\Resena;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResenaControllerTest extends TestCase
{
    use RefreshDatabase;

    private function createUser($role = 'USER')
    {
        return User::factory()->create(['role' => $role]);
    }

    private function createPelicula($attributes = [])
    {
        return Pelicula::factory()->create($attributes);
    }

    private function createResena($attributes = [])
    {
        return Resena::factory()->create($attributes);
    }


    public function test_show_incluye_lista_de_resenas()
    {
        $pelicula = $this->createPelicula();

        $users = User::factory()->count(3)->create();

        $resenas = $users->map(function ($user) use ($pelicula) {
            return $this->createResena([
                'pelicula_id' => $pelicula->id,
                'user_id' => $user->id,
            ]);
        });

        $response = $this->get(route('peliculas.show', $pelicula->id));

        $response->assertStatus(200)
            ->assertViewIs('peliculas.show')
            ->assertViewHas('pelicula', fn($viewPelicula) => $viewPelicula->id === $pelicula->id);

        foreach ($resenas as $resena) {
            $response->assertSee($resena->comentario)
                ->assertSee($resena->calificacion)
                ->assertSee($resena->user->username);
        }
    }

    public function test_store_crea_resena_para_usuario_autenticado()
    {
        $user = $this->createUser();
        $pelicula = $this->createPelicula();

        $data = [
            'calificacion' => 4,
            'comentario' => 'Excelente película.',
        ];

        $response = $this->actingAs($user)->post(route('resenas.store', $pelicula->id), $data);

        $response->assertRedirect();
        $this->assertDatabaseHas('resenas', [
            'pelicula_id' => $pelicula->id,
            'user_id' => $user->id,
            'calificacion' => 4,
            'comentario' => 'Excelente película.',
        ]);
    }

    public function test_store_no_crea_resena_si_ya_existe_para_usuario()
    {
        $user = $this->createUser();
        $pelicula = $this->createPelicula();

        $this->createResena([
            'pelicula_id' => $pelicula->id,
            'user_id' => $user->id,
            'calificacion' => 4,
            'comentario' => 'Primera reseña.',
        ]);

        $data = [
            'calificacion' => 5,
            'comentario' => 'Nueva reseña.',
        ];

        $response = $this->actingAs($user)->post(route('resenas.store', $pelicula->id), $data);

        $response->assertSessionHasErrors();
        $this->assertDatabaseMissing('resenas', [
            'pelicula_id' => $pelicula->id,
            'user_id' => $user->id,
            'calificacion' => 5,
        ]);
    }

    public function test_store_no_crea_resena_para_usuario_no_autenticado()
    {
        $pelicula = $this->createPelicula();

        $data = [
            'calificacion' => 4,
            'comentario' => 'Buena película.',
        ];

        $response = $this->post(route('resenas.store', $pelicula->id), $data);

        $response->assertRedirect(route('login'));
        $this->assertDatabaseMissing('resenas', [
            'pelicula_id' => $pelicula->id,
            'calificacion' => 4,
        ]);
    }

    public function test_destroy_elimina_resena_para_usuario_autorizado()
    {
        $user = $this->createUser();
        $pelicula = $this->createPelicula();
        $resena = Resena::factory()->create([
            'user_id' => $user->id,
            'pelicula_id' => $pelicula->id,
        ]);

        $response = $this->actingAs($user)->delete(route('resenas.destroy', $resena->id));

        $response->assertRedirect();
        $this->assertDatabaseMissing('resenas', ['id' => $resena->id]);
    }

    public function test_destroy_no_elimina_resena_para_usuario_no_autorizado()
    {
        $user = $this->createUser();
        $otraResena = $this->createResena();

        $response = $this->actingAs($user)->delete(route('resenas.destroy', $otraResena->id));

        $response->assertStatus(403);
        $this->assertDatabaseHas('resenas', ['id' => $otraResena->id]);
    }

    public function test_destroy_elimina_resena_para_admin()
    {
        $admin = $this->createUser('ADMIN');
        $resena = $this->createResena();

        $response = $this->actingAs($admin)->delete(route('resenas.destroy', $resena->id));

        $response->assertRedirect();
        $this->assertDatabaseMissing('resenas', ['id' => $resena->id]);
    }

    public function test_destroy_no_elimina_resena_para_usuario_no_autenticado()
    {
        $resena = $this->createResena();

        $response = $this->delete(route('resenas.destroy', $resena->id));

        $response->assertRedirect(route('login'));
        $this->assertDatabaseHas('resenas', ['id' => $resena->id]);
    }

}
