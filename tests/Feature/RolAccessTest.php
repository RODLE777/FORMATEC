<?php

namespace Tests\Feature;

use App\Models\Curso;
use App\Models\Grupo;
use App\Models\Profesor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RolAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_root_can_access_usuarios(): void
    {
        $root = User::factory()->root()->create();

        $this->actingAs($root)->get('/usuarios')->assertOk();
    }

    public function test_administrador_cannot_access_usuarios(): void
    {
        $admin = User::factory()->create(['rol' => 'ADMINISTRADOR']);

        $this->actingAs($admin)->get('/usuarios')->assertForbidden();
    }

    public function test_profesor_cannot_access_usuarios(): void
    {
        $profesor = User::factory()->profesor()->create();

        $this->actingAs($profesor)->get('/usuarios')->assertForbidden();
    }

    public function test_profesor_cannot_create_grupo(): void
    {
        $user = User::factory()->profesor()->create();
        Profesor::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)->get('/grupos/create')->assertForbidden();
    }

    public function test_administrador_can_create_grupo(): void
    {
        $admin = User::factory()->create(['rol' => 'ADMINISTRADOR']);

        $this->actingAs($admin)->get('/grupos/create')->assertOk();
    }

    public function test_profesor_only_sees_their_own_groups(): void
    {
        $userA = User::factory()->profesor()->create();
        $profesorA = Profesor::factory()->create(['user_id' => $userA->id, 'nombres' => 'Ana', 'apellidos' => 'Lopez']);

        $userB = User::factory()->profesor()->create();
        $profesorB = Profesor::factory()->create(['user_id' => $userB->id, 'nombres' => 'Beto', 'apellidos' => 'Ruiz']);

        $curso = Curso::factory()->create();
        $grupoDeA = Grupo::factory()->create(['curso_id' => $curso->id, 'profesor_id' => $profesorA->id, 'codigo_grupo' => 'A-1']);
        $grupoDeB = Grupo::factory()->create(['curso_id' => $curso->id, 'profesor_id' => $profesorB->id, 'codigo_grupo' => 'B-1']);

        $response = $this->actingAs($userA)->get('/grupos');

        $response->assertOk();
        $response->assertSee('A-1');
        $response->assertDontSee('B-1');
    }

    public function test_profesor_cannot_view_another_profesors_group_directly(): void
    {
        $userA = User::factory()->profesor()->create();
        Profesor::factory()->create(['user_id' => $userA->id]);

        $userB = User::factory()->profesor()->create();
        $profesorB = Profesor::factory()->create(['user_id' => $userB->id]);

        $curso = Curso::factory()->create();
        $grupoDeB = Grupo::factory()->create(['curso_id' => $curso->id, 'profesor_id' => $profesorB->id]);

        $this->actingAs($userA)->get("/grupos/{$grupoDeB->id}")->assertForbidden();
    }
}
