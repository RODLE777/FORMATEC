<?php

namespace App\Imports;

use App\Models\Curso;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

/** Columnas esperadas: nombre | descripcion */
class CursosImport implements ToCollection, WithHeadingRow
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
                $nombre = trim((string) $row['nombre']);
                if ($nombre === '') {
                    throw new \RuntimeException('nombre vacio');
                }

                Curso::firstOrCreate(
                    ['nombre' => $nombre],
                    ['descripcion' => $row['descripcion'] ?? null, 'activo' => true]
                );

                $this->filasImportadas++;
            } catch (\Throwable $e) {
                $this->errores[] = ['fila' => $numeroFila, 'error' => $e->getMessage()];
            }
        }
    }
}
