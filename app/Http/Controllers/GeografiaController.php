<?php

namespace App\Http\Controllers;

use App\Models\Departamento;
use App\Models\Distrito;
use App\Models\Municipio;
use Illuminate\Http\Request;

/**
 * Catalogo geografico: solo ROOT. Viene sembrado con los 16 municipios
 * de Cuscatlan; este modulo permite agregar/editar/eliminar distritos
 * y municipios cuando haga falta afinar el catalogo real.
 */
class GeografiaController extends Controller
{
    public function index()
    {
        $departamentos = Departamento::with('municipios.distritos')->orderBy('nombre')->get();

        return view('departamentos.index', compact('departamentos'));
    }

    public function storeMunicipio(Request $request)
    {
        $data = $request->validate([
            'departamento_id' => 'required|exists:departamentos,id',
            'nombre' => 'required|string|max:100',
        ]);

        Municipio::create($data);

        return back()->with('status', 'Municipio agregado correctamente.');
    }
    public function updateMunicipio(Request $request, Municipio $municipio)
    {
        $data = $request->validate(['nombre' => 'required|string|max:100']);

        $municipio->update($data);

        return back()->with('status', 'Municipio actualizado correctamente.');
    }

    /**
     * Un municipio con distritos NO se puede eliminar directamente: la
     * FK distritos.municipio_id es RESTRICT (a proposito, para no
     * borrar por accidente toda una jerarquia). Hay que vaciarlo de
     * distritos primero.
     */
    public function destroyMunicipio(Municipio $municipio)
    {
        $totalDistritos = $municipio->distritos()->count();

        if ($totalDistritos > 0) {
            return back()->with('error', "No se puede eliminar '{$municipio->nombre}': todavia tiene {$totalDistritos} distrito(s). Eliminalos primero.");
        }

        $afectados = \App\Models\Estudiante::where('municipio_id', $municipio->id)->count();

        $municipio->delete();

        $mensaje = 'Municipio eliminado.';
        if ($afectados > 0) {
            $mensaje .= " {$afectados} estudiante(s) quedaron sin municipio asignado — puedes reasignarlos desde su ficha.";
        }

        return back()->with('status', $mensaje);
    }

    public function storeDistrito(Request $request)
    {
        $data = $request->validate([
            'municipio_id' => 'required|exists:municipios,id',
            'nombre' => 'required|string|max:100',
        ]);

        Distrito::create($data);

        return back()->with('status', 'Distrito agregado correctamente.');
    }

    public function updateDistrito(Request $request, Distrito $distrito)
    {
        $data = $request->validate(['nombre' => 'required|string|max:100']);

        $distrito->update($data);

        return back()->with('status', 'Distrito actualizado correctamente.');
    }

    public function destroyDistrito(Distrito $distrito)
    {
        $afectados = \App\Models\Estudiante::where('distrito_id', $distrito->id)->count();

        $distrito->delete();

        $mensaje = 'Distrito eliminado.';
        if ($afectados > 0) {
            $mensaje .= " {$afectados} estudiante(s) quedaron sin distrito asignado — puedes reasignarlos desde su ficha.";
        }

        return back()->with('status', $mensaje);
    }
}
