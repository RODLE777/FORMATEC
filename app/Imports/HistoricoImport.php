<?php

namespace App\Imports;

use App\Models\Curso;
use App\Models\Estudiante;
use App\Models\Grupo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

/**
 * Migra registros historicos (cursos ya finalizados antes de usar el
 * sistema). Columnas esperadas: nombres | apellidos | sexo |
 * fecha_nacimiento | dui | curso | codigo_grupo | anio | mes |
 * nota_final | resultado_final (GRADUADO/DESERTADO/REPROBADO) |
 * fecha_inscripcion
 *
 * Crea curso/grupo/estudiante si no existen. Registra UNA evaluacion
 * "Historico" con la nota dada -para que el trigger calcule
 * nota_final igual que en cualquier otro flujo- y despues fija
 * resultado_final con el valor real del historico (que puede ser
 * DESERTADO, algo que el trigger nunca asigna solo).
 */
class HistoricoImport implements ToCollection, WithHeadingRow
{
    public int $totalFilas = 0;
    public int $filasImportadas = 0;
    public array $errores = [];

    public function collection(Collection $rows): void
    {
        $this->totalFilas = $rows->count();

        foreach ($rows as $index => $row) {
            $numeroFila = $index + 2;

            try {
                $this->importarFila($row);
                $this->filasImportadas++;
            } catch (\Throwable $e) {
                $this->errores[] = ['fila' => $numeroFila, 'error' => $e->getMessage()];
            }
        }
    }

    private function importarFila(Collection $row): void
    {
        $resultado = strtoupper(trim((string) $row['resultado_final']));
        if (! in_array($resultado, ['GRADUADO', 'DESERTADO', 'REPROBADO'], true)) {
            throw new \RuntimeException("resultado_final invalido: '{$resultado}'");
        }

        $curso = Curso::firstOrCreate(['nombre' => trim((string) $row['curso'])]);

        $grupo = Grupo::firstOrCreate(
            ['codigo_grupo' => trim((string) $row['codigo_grupo']), 'anio' => (int) $row['anio']],
            [
                'curso_id' => $curso->id,
                'mes' => (int) ($row['mes'] ?? 1),
                'numero_evaluaciones' => 1,
                'estado' => 'FINALIZADO',
            ]
        );

        $dui = trim((string) ($row['dui'] ?? ''));
        $estudiante = $dui !== ''
            ? Estudiante::where('dui', $dui)->first()
            : null;

        if (! $estudiante) {
            $estudiante = Estudiante::firstOrCreate([
                'nombres' => trim((string) $row['nombres']),
                'apellidos' => trim((string) $row['apellidos']),
                'fecha_nacimiento' => Carbon::parse($row['fecha_nacimiento']),
            ], [
                'sexo' => strtoupper(trim((string) $row['sexo'])),
                'dui' => $dui ?: null,
                'activo' => true,
            ]);
        }

        $inscripcion = $grupo->inscripciones()->firstOrCreate(
            ['estudiante_id' => $estudiante->id],
            [
                'fecha_inscripcion' => Carbon::parse($row['fecha_inscripcion'] ?? now()),
                'estado' => $resultado === 'DESERTADO' ? 'RETIRADA' : 'FINALIZADA',
                'resultado_final' => 'EN_CURSO', // se corrige abajo, despues de crear la nota
            ]
        );

        $notaFinal = (float) $row['nota_final'];

        // Esta evaluacion dispara el trigger, que calcula nota_final
        // (con 1 sola evaluacion, el promedio = la nota dada) y de
        // paso intenta auto-decidir GRADUADO/REPROBADO.
        $inscripcion->evaluaciones()->updateOrCreate(
            ['numero_evaluacion' => 1],
            ['nombre_evaluacion' => 'Historico', 'nota' => $notaFinal, 'fecha_evaluacion' => now()->toDateString()]
        );

        // El historico manda: si el resultado real fue DESERTADO (o
        // simplemente para que quede exacto), se fija explicitamente
        // encima de lo que el trigger haya inferido.
        $inscripcion->update([
            'resultado_final' => $resultado,
            'fecha_finalizacion' => Carbon::parse($row['fecha_inscripcion'] ?? now()),
        ]);
    }
}
