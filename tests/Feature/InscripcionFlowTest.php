<?php

namespace Tests\Feature;

use App\Models\Curso;
use App\Models\Estudiante;
use App\Models\Grupo;
use App\Models\Inscripcion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;


class InscripcionFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_cupo_maximo_blocks_new_enrollment_when_full(): void
    {
        $admin = User::factory()->create(['rol' => 'ADMINISTRADOR']);
        $curso = Curso::factory()->create();
        $grupo = Grupo::factory()->create(['curso_id' => $curso->id, 'cupo_maximo' => 1]);

        $primerEstudiante = Estudiante::factory()->create();
        Inscripcion::create([
            'estudiante_id' => $primerEstudiante->id,
            'grupo_id' => $grupo->id,
            'fecha_inscripcion' => now(),
            'estado' => 'ACTIVA',
            'resultado_final' => 'EN_CURSO',
        ]);

        $segundoEstudiante = Estudiante::factory()->create();

        $response = $this->actingAs($admin)->post('/inscripciones', [
            'grupo_id' => $grupo->id,
            'estudiante_id' => $segundoEstudiante->id,
            'fecha_inscripcion' => now()->toDateString(),
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('inscripciones', ['estudiante_id' => $segundoEstudiante->id]);
    }

    public function test_admin_can_enroll_student_when_cupo_available(): void
    {
        $admin = User::factory()->create(['rol' => 'ADMINISTRADOR']);
        $curso = Curso::factory()->create();
        $grupo = Grupo::factory()->create(['curso_id' => $curso->id, 'cupo_maximo' => 5]);
        $estudiante = Estudiante::factory()->create();

        $response = $this->actingAs($admin)->post('/inscripciones', [
            'grupo_id' => $grupo->id,
            'estudiante_id' => $estudiante->id,
            'fecha_inscripcion' => now()->toDateString(),
        ]);

        $response->assertRedirect(route('grupos.show', $grupo));
        $this->assertDatabaseHas('inscripciones', [
            'estudiante_id' => $estudiante->id,
            'grupo_id' => $grupo->id,
            'resultado_final' => 'EN_CURSO',
        ]);
    }

    public function test_estudiante_edad_accessor_calculates_correctly(): void
    {
        $estudiante = Estudiante::factory()->create([
            'fecha_nacimiento' => now()->subYears(20)->subDays(1),
        ]);

        $this->assertEquals(20, $estudiante->edad);
        $this->assertFalse($estudiante->es_menor_edad);
    }

    public function test_estudiante_menor_de_edad_accessor(): void
    {
        $estudiante = Estudiante::factory()->create([
            'fecha_nacimiento' => now()->subYears(16),
        ]);

        $this->assertTrue($estudiante->es_menor_edad);
    }
}
