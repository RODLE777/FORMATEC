<?php

namespace App\Http\Controllers;

use App\Models\Curso;
use App\Models\ReporteGenerado;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * El TOTALON es el UNICO reporte que se queda fuera del workspace de
 * un grupo, porque por definicion consolida datos de VARIOS grupos y
 * periodos (matricula, desercion, graduacion por curso/mes/año). Vive
 * en Administracion. Se apoya en las vistas SQL vista_totalon_mensual
 * y vista_totalon_anual creadas en la migracion de vistas.
 */
class TotalonController extends Controller
{
    public function index(Request $request)
    {
        $anio = $request->integer('anio') ?: now()->year;
        $vista = $request->get('periodo', 'anual') === 'mensual' ? 'vista_totalon_mensual' : 'vista_totalon_anual';

        $filas = DB::table($vista)->where('anio', $anio)->orderBy('curso')->get();

        return view('reportes.totalon', [
            'filas' => $filas,
            'anio' => $anio,
            'periodo' => $request->get('periodo', 'anual'),
            'aniosDisponibles' => DB::table('grupos')->distinct()->orderByDesc('anio')->pluck('anio'),
            'cursos' => Curso::orderBy('nombre')->get(),
        ]);
    }

    public function pdf(Request $request)
    {
        $anio = $request->integer('anio') ?: now()->year;
        $vista = $request->get('periodo', 'anual') === 'mensual' ? 'vista_totalon_mensual' : 'vista_totalon_anual';

        $filas = DB::table($vista)->where('anio', $anio)->orderBy('curso')->get();

        $pdf = Pdf::loadView('reportes.totalon-pdf', ['filas' => $filas, 'anio' => $anio])->setPaper('letter', 'landscape');

        ReporteGenerado::create([
            'usuario_id' => $request->user()->id,
            'tipo_reporte' => 'TOTALON',
            'parametros' => ['anio' => $anio, 'periodo' => $request->get('periodo', 'anual')],
        ]);

        return $pdf->download("totalon-{$anio}.pdf");
    }

    public function excel(Request $request)
    {
        $anio = $request->integer('anio') ?: now()->year;
        $mensual = $request->get('periodo', 'anual') === 'mensual';
        $vista = $mensual ? 'vista_totalon_mensual' : 'vista_totalon_anual';

        $filas = DB::table($vista)->where('anio', $anio)->orderBy('curso')->get();

        ReporteGenerado::create([
            'usuario_id' => $request->user()->id,
            'tipo_reporte' => 'TOTALON_EXCEL',
            'parametros' => ['anio' => $anio, 'periodo' => $request->get('periodo', 'anual')],
        ]);

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\TotalonExport($filas, $mensual),
            "totalon-{$anio}.xlsx"
        );
    }
}
