<?php

namespace Tests\Feature;

use App\Models\Resena;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserDashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    private function createUser($role = 'USER')
    {
        return User::factory()->create(['role' => $role]);
    }

    private function createResena($attributes = [])
    {
        return Resena::factory()->create($attributes);
    }

    public function test_index_muestra_resenas_del_usuario_autenticado()
    {
        $user = $this->createUser();
        $this->actingAs($user);


        $userResenas = Resena::factory()->count(3)->create(['user_id' => $user->id]);
        Resena::factory()->count(2)->create();

        $response = $this->get(route('user.dashboard'));

        $response->assertStatus(200)
            ->assertViewIs('user.dashboard')
            ->assertViewHas('resenas', function ($resenas) use ($userResenas) {
                return $resenas->pluck('id')->diff($userResenas->pluck('id'))->isEmpty();
            });


        foreach ($userResenas as $resena) {
            $response->assertSee($resena->comentario);
        }
    }

    public function test_index_no_permitido_para_usuario_no_autenticado()
    {
        $response = $this->get(route('user.dashboard'));

        $response->assertRedirect(route('login'));
    }
}
