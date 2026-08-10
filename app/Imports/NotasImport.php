<?php

namespace App\Imports;

use App\Models\Grupo;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

/**
 * Columnas esperadas: codigo_grupo | anio | codigo_formatec |
 * numero_evaluacion | nota | nombre_evaluacion
 *
 * El estudiante debe estar YA inscrito en ese grupo (esta importacion
 * registra notas, no inscribe). nota_final se recalcula solo via el
 * trigger de evaluaciones, igual que si se cargara desde la pantalla.
 */
class NotasImport implements ToCollection, WithHeadingRow
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
                $grupo = Grupo::where('codigo_grupo', trim((string) $row['codigo_grupo']))
                    ->where('anio', (int) $row['anio'])
                    ->first();

                if (! $grupo) {
                    throw new \RuntimeException("grupo '{$row['codigo_grupo']}' ({$row['anio']}) no existe");
                }

                $inscripcion = $grupo->inscripciones()
                    ->whereHas('estudiante', fn ($q) => $q->where('codigo_formatec', trim((string) $row['codigo_formatec'])))
                    ->first();

                if (! $inscripcion) {
                    throw new \RuntimeException("estudiante '{$row['codigo_formatec']}' no esta inscrito en ese grupo");
                }

                $numero = (int) $row['numero_evaluacion'];
                $nota = (float) $row['nota'];

                if ($nota < 0 || $nota > 10) {
                    throw new \RuntimeException("nota fuera de rango (0-10): {$nota}");
                }

                $inscripcion->evaluaciones()->updateOrCreate(
                    ['numero_evaluacion' => $numero],
                    [
                        'nombre_evaluacion' => $row['nombre_evaluacion'] ?? "Evaluacion {$numero}",
                        'nota' => $nota,
                        'fecha_evaluacion' => now()->toDateString(),
                    ]
                );

                $this->filasImportadas++;
            } catch (\Throwable $e) {
                $this->errores[] = ['fila' => $numeroFila, 'error' => $e->getMessage()];
            }
        }
    }
}
