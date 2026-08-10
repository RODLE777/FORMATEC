<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TotalonExport implements FromCollection, WithHeadings
{
    public function __construct(private Collection $filas, private bool $mensual) {}

    public function collection(): Collection
    {
        return $this->filas->map(function ($f) {
            $base = ['curso' => $f->curso];
            if ($this->mensual) {
                $base['mes'] = $f->mes;
            }

            return array_merge($base, [
                'numero_de_cursos' => $f->numero_de_cursos,
                'iniciaron_masculino' => $f->iniciaron_masculino,
                'iniciaron_femenino' => $f->iniciaron_femenino,
                'iniciaron_total' => $f->iniciaron_total,
                'desertados_total' => $f->desertados_total,
                'graduados_total' => $f->graduados_total,
            ]);
        });
    }

    public function headings(): array
    {
        $base = ['Curso'];
        if ($this->mensual) {
            $base[] = 'Mes';
        }

        return array_merge($base, ['# Grupos', 'Iniciaron M', 'Iniciaron F', 'Iniciaron Total', 'Desertaron', 'Graduados']);
    }
}
