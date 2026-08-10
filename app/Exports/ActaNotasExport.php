<?php

namespace App\Exports;

use App\Models\Grupo;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ActaNotasExport implements FromCollection, WithHeadings
{
    public function __construct(private Grupo $grupo) {}

    public function collection()
    {
        return $this->grupo->inscripciones->map(function ($inscripcion) {
            $fila = ['estudiante' => $inscripcion->estudiante->nombre_completo];

            for ($n = 1; $n <= $this->grupo->numero_evaluaciones; $n++) {
                $fila["eval_{$n}"] = $inscripcion->evaluaciones->firstWhere('numero_evaluacion', $n)->nota ?? null;
            }

            $fila['nota_final'] = $inscripcion->nota_final;
            $fila['resultado'] = $inscripcion->resultado_final;

            return $fila;
        });
    }

    public function headings(): array
    {
        $headings = ['Estudiante'];
        for ($n = 1; $n <= $this->grupo->numero_evaluaciones; $n++) {
            $headings[] = "Evaluacion {$n}";
        }
        $headings[] = 'Nota final';
        $headings[] = 'Resultado';

        return $headings;
    }
}
