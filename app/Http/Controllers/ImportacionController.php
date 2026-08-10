<?php

namespace App\Http\Controllers;

use App\Imports\CursosImport;
use App\Imports\EstudiantesImport;
use App\Imports\HistoricoImport;
use App\Imports\NotasImport;
use App\Models\Importacion;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Importacion masiva desde Excel: exclusiva de ROOT (seccion 5 del
 * prompt maestro). Soporta los 4 tipos definidos en el esquema.
 */
class ImportacionController extends Controller
{
    private const IMPORTS = [
        'ESTUDIANTES' => EstudiantesImport::class,
        'CURSOS' => CursosImport::class,
        'NOTAS' => NotasImport::class,
        'HISTORICO' => HistoricoImport::class,
    ];

    public function index()
    {
        $importaciones = Importacion::with('usuario')->latest('created_at')->paginate(15);

        return view('importaciones.index', compact('importaciones'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'archivo' => 'required|file|mimes:xlsx,xls,csv|max:5120',
            'tipo' => 'required|in:ESTUDIANTES,CURSOS,NOTAS,HISTORICO',
        ]);

        $importClass = self::IMPORTS[$request->tipo];
        $import = new $importClass;

        try {
            Excel::import($import, $request->file('archivo'));
            $estado = 'COMPLETADO';
        } catch (\Throwable $e) {
            $estado = 'ERROR';
            $import->errores[] = ['fila' => '—', 'error' => 'Archivo invalido: '.$e->getMessage()];
        }

        Importacion::create([
            'usuario_id' => $request->user()->id,
            'archivo_nombre' => $request->file('archivo')->getClientOriginalName(),
            'tipo' => $request->tipo,
            'total_filas' => $import->totalFilas,
            'filas_importadas' => $import->filasImportadas,
            'filas_error' => count($import->errores),
            'estado' => $estado,
            'detalle_errores' => $import->errores ?: null,
        ]);

        $mensaje = "Se importaron {$import->filasImportadas} de {$import->totalFilas} filas.";
        if (count($import->errores) > 0) {
            $mensaje .= ' '.count($import->errores).' fila(s) con error — revisa el detalle abajo.';
        }

        return redirect()->route('importaciones.index')->with('status', $mensaje);
    }
}
