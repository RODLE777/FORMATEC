<?php

namespace App\Http\Controllers;

use App\Models\Inscripcion;
use Illuminate\Http\Request;

class ResultadosController extends Controller
{
    public function index(Request $request)
    {
        $resultado = $request->get('resultado', 'GRADUADO');

        $inscripciones = Inscripcion::with(['estudiante', 'grupo.curso'])
            ->where('resultado_final', $resultado)
            ->when($request->anio, fn ($q) => $q->whereHas('grupo', fn ($qq) => $qq->where('anio', $request->anio)))
            ->latest('fecha_finalizacion')
            ->paginate(30)
            ->withQueryString();

        return view('reportes.resultados', [
            'inscripciones' => $inscripciones,
            'resultado' => $resultado,
        ]);
    }
}
