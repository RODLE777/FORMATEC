<?php

namespace app\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TotalonService
{
    public const CAMPOS = [
        'numero_de_cursos',
        'iniciaron_masculino',
        'iniciaron_femenino',
        'iniciaron_total',
        'desertados_masculino',
        'desertados_femenino',
        'desertados_total',
        'graduados_masculino',
        'graduados_femenino',
        'graduados_total',
    ];

    public function generar(
        int $anio,
        int $mesInicio,
        int $mesFin,
        ?int $cursoId = null
    ): array {
        $inicio = Carbon::create($anio, $mesInicio, 1)
            ->startOfDay();

        $fin = Carbon::create($anio, $mesFin, 1)
            ->endOfMonth()
            ->endOfDay();

        // Grupos que tienen actividad dentro del rango.
        // Incluye los no finalizados para mostrar advertencias.
        $consulta = DB::table('grupos as g')
            ->join('cursos as c', 'c.id', '=', 'g.curso_id')
            ->select(
                'g.id',
                'g.curso_id',
                'g.codigo_grupo',
                'g.estado',
                'g.fecha_inicio',
                'g.fecha_fin',
                'c.nombre as curso'
            )
            ->whereDate('g.fecha_inicio', '<=', $fin->toDateString())
            ->whereDate('g.fecha_fin', '>=', $inicio->toDateString());

        if ($cursoId !== null) {
            $consulta->where('g.curso_id', $cursoId);
        }

        $grupos = $consulta
            ->orderBy('c.nombre')
            ->orderBy('g.codigo_grupo')
            ->get();

        // Solo grupos finalizados cuya fecha de fin
        // pertenece al período del informe.
        $incluidos = $grupos->filter(
            fn ($g) =>
                $g->estado === 'FINALIZADO'
                && Carbon::parse($g->fecha_fin)->betweenIncluded(
                    $inicio,
                    $fin
                )
        )->values();

        $ids = $incluidos->pluck('id')->all();

        // Cada inscripción representa una participación.
        $inscripciones = empty($ids)
            ? collect()
            : DB::table('inscripciones as i')
                ->join(
                    'estudiantes as e',
                    'e.id',
                    '=',
                    'i.estudiante_id'
                )
                ->select(
                    'i.id',
                    'i.grupo_id',
                    'i.estudiante_id',
                    'i.estado',
                    'i.resultado_final',
                    'e.sexo'
                )
                ->whereIn('i.grupo_id', $ids)
                ->get();

        $porGrupo = $inscripciones->groupBy('grupo_id');

        $filas = collect();
        $advertencias = collect();

        foreach ($incluidos as $grupo) {
            $participaciones = $porGrupo->get(
                $grupo->id,
                collect()
            );

            if (!$filas->has($grupo->curso_id)) {
                $fila = [
                    'curso_id' => $grupo->curso_id,
                    'curso' => $grupo->curso,
                ];

                foreach (self::CAMPOS as $campo) {
                    $fila[$campo] = 0;
                }

                $filas->put($grupo->curso_id, $fila);
            }

            $fila = $filas->get($grupo->curso_id);
            $fila['numero_de_cursos']++;

            foreach ($participaciones as $i) {
                $sexo = match (strtoupper((string) $i->sexo)) {
                    'MASCULINO' => 'masculino',
                    'FEMENINO' => 'femenino',
                    default => null,
                };

                if ($sexo === null) {
                    $advertencias->push([
                        'tipo' => 'sexo_no_valido',
                        'grupo_id' => $grupo->id,
                        'grupo' => $grupo->codigo_grupo,
                        'curso' => $grupo->curso,
                        'estudiante_id' => $i->estudiante_id,
                        'mensaje' => 'El estudiante no tiene un género válido para el desglose oficial.',
                    ]);

                    continue;
                }

                $fila["iniciaron_{$sexo}"]++;
                $fila['iniciaron_total']++;

                if ($i->resultado_final === 'DESERTADO') {
                    $fila["desertados_{$sexo}"]++;
                    $fila['desertados_total']++;
                }

                if ($i->resultado_final === 'GRADUADO') {
                    $fila["graduados_{$sexo}"]++;
                    $fila['graduados_total']++;
                }

                if ($i->resultado_final === 'EN_CURSO'
                    || $i->resultado_final === null) {
                    $advertencias->push([
                        'tipo' => 'resultado_pendiente',
                        'grupo_id' => $grupo->id,
                        'grupo' => $grupo->codigo_grupo,
                        'curso' => $grupo->curso,
                        'estudiante_id' => $i->estudiante_id,
                        'inscripcion_id' => $i->id,
                        'mensaje' => 'Inscripción sin resultado académico definitivo.',
                    ]);
                }

                if (
                    ($i->estado === 'RETIRADA'
                        && $i->resultado_final === 'GRADUADO')
                    || ($i->estado === 'FINALIZADA'
                        && $i->resultado_final === 'EN_CURSO')
                ) {
                    $advertencias->push([
                        'tipo' => 'estado_inconsistente',
                        'grupo_id' => $grupo->id,
                        'grupo' => $grupo->codigo_grupo,
                        'curso' => $grupo->curso,
                        'estudiante_id' => $i->estudiante_id,
                        'inscripcion_id' => $i->id,
                        'mensaje' => 'El estado de inscripción y el resultado académico requieren revisión.',
                    ]);
                }
            }

            $filas->put($grupo->curso_id, $fila);
        }

        $filas = $filas->sortBy('curso')
            ->values()
            ->map(fn ($fila) => (object) $fila);

        $totales = [];

        foreach (self::CAMPOS as $campo) {
            $totales[$campo] = $filas->sum($campo);
        }

        $excluidos = $grupos
            ->reject(fn ($g) => $incluidos->contains('id', $g->id))
            ->values()
            ->map(function ($g) use ($inicio, $fin) {
                $motivo = $g->estado !== 'FINALIZADO'
                    ? 'El grupo no tiene estado FINALIZADO.'
                    : 'La fecha de finalización está fuera del rango seleccionado.';

                return (object) [
                    'id' => $g->id,
                    'curso' => $g->curso,
                    'codigo_grupo' => $g->codigo_grupo,
                    'estado' => $g->estado,
                    'fecha_inicio' => $g->fecha_inicio,
                    'fecha_fin' => $g->fecha_fin,
                    'motivo' => $motivo,
                ];
            });

        return [
            'filas' => $filas,
            'totales' => (object) $totales,
            'gruposIncluidos' => $incluidos,
            'gruposExcluidos' => $excluidos,
            'advertencias' => $advertencias,
            'periodo' => [
                'anio' => $anio,
                'mes_inicio' => $mesInicio,
                'mes_fin' => $mesFin,
                'fecha_inicio' => $inicio->toDateString(),
                'fecha_fin' => $fin->toDateString(),
            ],
        ];
    }
}
