<?php

namespace App\Http\Controllers;

use App\Models\Departamento;
use Illuminate\Http\Request;

/**
 * Catalogo geografico (departamentos/municipios/distritos): solo ROOT,
 * y normalmente de solo lectura porque ya viene sembrado (seed) con los
 * datos reales de Cuscatlan Sur. Se deja edicion disponible por si algun
 * dia se agrega un nuevo distrito.
 */
class GeografiaController extends Controller
{
    public function index()
    {
        $departamentos = Departamento::with('municipios.distritos')->orderBy('nombre')->get();

        return view('departamentos.index', compact('departamentos'));
    }

    public function storeDistrito(Request $request)
    {
        $data = $request->validate([
            'municipio_id' => 'required|exists:municipios,id',
            'nombre' => 'required|string|max:100',
        ]);

        \App\Models\Distrito::create($data);

        return back()->with('status', 'Distrito agregado correctamente.');
    }
}
