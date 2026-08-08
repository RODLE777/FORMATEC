<?php

namespace App\Imports;

use App\Models\Estudiante;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

/**
 * Importacion masiva de estudiantes (seccion 5: exclusiva de ROOT).
 * Se procesa fila por fila -en vez de con reglas globales- para poder
 * reportar exactamente que filas fallaron y por que, sin abortar todo
 * el archivo por un solo error (ImportacionController arma el resumen
 * que se guarda en importaciones.detalle_errores).
 *
 * Columnas esperadas (encabezados en fila 1, insensible a mayusculas):
 * nombres | apellidos | sexo | fecha_nacimiento | dui | correo |
 * telefono_celular | departamento | municipio | distrito
 */
class EstudiantesImport implements ToCollection, WithHeadingRow
{
    public int $totalFilas = 0;

    public int $filasImportadas = 0;

    public array $errores = [];

    public function collection(Collection $rows): void
    {
        $this->totalFilas = $rows->count();

        foreach ($rows as $index => $row) {
            $numeroFila = $index + 2; // +1 por heading row, +1 por index base 0

            try {
                $this->importarFila($row, $numeroFila);
                $this->filasImportadas++;
            } catch (\Throwable $e) {
                $this->errores[] = [
                    'fila' => $numeroFila,
                    'error' => $e->getMessage(),
                ];
            }
        }
    }

    private function importarFila(Collection $row, int $numeroFila): void
    {
        $nombres = trim((string) $row['nombres']);
        $apellidos = trim((string) $row['apellidos']);
        $sexo = strtoupper(trim((string) $row['sexo']));
        $fechaNacimiento = $row['fecha_nacimiento'];

        if ($nombres === '' || $apellidos === '') {
            throw new \RuntimeException('nombres/apellidos vacios');
        }

        if (! in_array($sexo, ['MASCULINO', 'FEMENINO'], true)) {
            throw new \RuntimeException("sexo invalido: '{$sexo}' (debe ser MASCULINO o FEMENINO)");
        }

        $fecha = Carbon::parse($fechaNacimiento);

        // codigo_formatec NUNCA se importa: lo genera el trigger al insertar.
        Estudiante::create([
            'nombres' => $nombres,
            'apellidos' => $apellidos,
            'sexo' => $sexo,
            'fecha_nacimiento' => $fecha,
            'dui' => $row['dui'] ?? null,
            'correo' => $row['correo'] ?? null,
            'telefono_celular' => $row['telefono_celular'] ?? null,
            'activo' => true,
        ]);
    }
}
