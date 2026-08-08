<?php

namespace App\Http\Controllers;

use App\Imports\EstudiantesImport;
use App\Models\Importacion;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Importacion masiva desde Excel: exclusiva de ROOT (seccion 5 del
 * prompt maestro). No se importan notas ni cursos aqui todavia -solo
 * estudiantes-, dejando el patron listo para extender por tipo.
 */
class ImportacionController extends Controller
{
    public function index()
    {
        $importaciones = Importacion::with('usuario')->latest('created_at')->paginate(15);

        return view('importaciones.index', compact('importaciones'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'archivo' => 'required|file|mimes:xlsx,xls,csv|max:5120',
            'tipo' => 'required|in:ESTUDIANTES',
        ]);

        $import = new EstudiantesImport;

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
