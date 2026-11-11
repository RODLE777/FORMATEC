<?php

namespace app\Http\Controllers;

use App\Exports\TotalonExport;
use App\Models\Curso;
use App\Models\ReporteGenerado;
use App\Services\TotalonService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

class TotalonController extends Controller
{
    private const MESES = [
        1 => 'Enero',
        2 => 'Febrero',
        3 => 'Marzo',
        4 => 'Abril',
        5 => 'Mayo',
        6 => 'Junio',
        7 => 'Julio',
        8 => 'Agosto',
        9 => 'Septiembre',
        10 => 'Octubre',
        11 => 'Noviembre',
        12 => 'Diciembre',
    ];

    public function __construct(
        private TotalonService $totalonService
    ) {
    }

    private function filtros(Request $request): array
    {
        $datos = $request->validate([
            'anio' => ['nullable', 'integer', 'between:2000,2100'],
            'mes_inicio' => ['nullable', 'integer', 'between:1,12'],
            'mes_fin' => ['nullable', 'integer', 'between:1,12'],
            'curso_id' => ['nullable', 'integer', 'exists:cursos,id'],
        ]);

        $anio = (int) ($datos['anio'] ?? now()->year);
        $mesInicio = (int) ($datos['mes_inicio'] ?? 1);
        $mesFin = (int) ($datos['mes_fin'] ?? 12);

        if ($mesInicio >= $mesFin) {
            throw ValidationException::withMessages([
                'mes_fin' => 'El TOTALON debe abarcar al menos dos meses del mismo año.',
            ]);
        }

        return [
            'anio' => $anio,
            'mes_inicio' => $mesInicio,
            'mes_fin' => $mesFin,
            'curso_id' => isset($datos['curso_id'])
                ? (int) $datos['curso_id']
                : null,
        ];
    }

    private function obtenerReporte(array $filtros): array
    {
        return $this->totalonService->generar(
            $filtros['anio'],
            $filtros['mes_inicio'],
            $filtros['mes_fin'],
            $filtros['curso_id']
        );
    }

    private function nombrePeriodo(array $filtros): string
    {
        return self::MESES[$filtros['mes_inicio']]
            . ' a '
            . self::MESES[$filtros['mes_fin']]
            . ' de '
            . $filtros['anio'];
    }

    private function registrarDescarga(
        Request $request,
        string $tipo,
        array $filtros
    ): void {
        ReporteGenerado::create([
            'usuario_id' => $request->user()->id,
            'tipo_reporte' => $tipo,
            'parametros' => $filtros,
        ]);
    }

    public function index(Request $request)
    {
        $filtros = $this->filtros($request);
        $reporte = $this->obtenerReporte($filtros);

        $anios = DB::table('grupos')
            ->whereNotNull('fecha_fin')
            ->selectRaw('YEAR(fecha_fin) as anio')
            ->distinct()
            ->pluck('anio')
            ->map(fn ($anio) => (int) $anio)
            ->push(now()->year)
            ->push($filtros['anio'])
            ->unique()
            ->sortDesc()
            ->values();

        return view('reportes.totalon', [
            'filas' => $reporte['filas'],
            'totales' => $reporte['totales'],
            'gruposIncluidos' => $reporte['gruposIncluidos'],
            'gruposExcluidos' => $reporte['gruposExcluidos'],
            'advertencias' => $reporte['advertencias'],
            'anio' => $filtros['anio'],
            'mesInicio' => $filtros['mes_inicio'],
            'mesFin' => $filtros['mes_fin'],
            'cursoId' => $filtros['curso_id'],
            'filtros' => $filtros,
            'nombrePeriodo' => $this->nombrePeriodo($filtros),
            'meses' => self::MESES,
            'aniosDisponibles' => $anios,
            'cursos' => Curso::orderBy('nombre')->get(),
        ]);
    }

    public function pdf(Request $request)
    {
        $filtros = $this->filtros($request);
        $reporte = $this->obtenerReporte($filtros);

        $pdf = Pdf::loadView('reportes.totalon-pdf', [
            'filas' => $reporte['filas'],
            'totales' => $reporte['totales'],
            'nombrePeriodo' => $this->nombrePeriodo($filtros),
            'anio' => $filtros['anio'],
            'cursoSeleccionado' => $filtros['curso_id']
                ? Curso::find($filtros['curso_id'])?->nombre
                : null,
        ])->setPaper('letter', 'landscape');

        $nombre = sprintf(
            'TOTALON_%d_%02d_%02d.pdf',
            $filtros['anio'],
            $filtros['mes_inicio'],
            $filtros['mes_fin']
        );

        $this->registrarDescarga(
            $request,
            'TOTALON',
            $filtros
        );

        return $pdf->download($nombre);
    }

    public function excel(Request $request)
    {
        $filtros = $this->filtros($request);
        $reporte = $this->obtenerReporte($filtros);

        $nombre = sprintf(
            'TOTALON_%d_%02d_%02d.xlsx',
            $filtros['anio'],
            $filtros['mes_inicio'],
            $filtros['mes_fin']
        );

        $exportacion = new TotalonExport(
            $reporte['filas'],
            $reporte['totales'],
            $this->nombrePeriodo($filtros)
        );

        $this->registrarDescarga(
            $request,
            'TOTALON_EXCEL',
            $filtros
        );

        return Excel::download($exportacion, $nombre);
    }
}
