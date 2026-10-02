<?php

namespace App\Http\Controllers;

use App\Models\Estudiante;
use App\Models\Grupo;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class KioscoController extends Controller
{
    public function index()
{
    $estadosAbiertos = ['PLANIFICADO', 'EN CURSO'];

    $grupos = Grupo::with(['curso', 'profesor'])
        ->whereIn('estado', $estadosAbiertos)
        ->whereHas('curso', fn ($q) => $q->where('activo', true))
        ->orderBy('id')
        ->get()
        ->filter(function (Grupo $grupo) {
            $disponibles = $grupo->cuposDisponibles();
            return is_null($disponibles) || $disponibles > 0;
        })
        ->values();

    return view('kiosco.index', compact('grupos'));
}

    public function registrar(Request $request)
    {
        // 1) Validación base
        $data = $request->validate([
            'nombres'          => 'required|string|max:150',
            'apellidos'        => 'required|string|max:150',
            'sexo'             => 'required|in:MASCULINO,FEMENINO',
            'fecha_nacimiento' => 'required|date|before:today',
            'correo'           => 'nullable|email|max:150',
            'telefono_celular' => 'nullable|string|max:20',
            'direccion'        => 'nullable|string|max:255',
            'profesion_oficio' => 'nullable|string|max:150',
            'nivel_estudio'    => 'nullable|string|max:100',
            'grupo_id'         => 'required|exists:grupos,id',
        ]);

        $esMenor = Carbon::parse($data['fecha_nacimiento'])->age < 18;

        // 2) DUI: obligatorio si es mayor de edad
        $request->validate([
            'dui' => [
                $esMenor ? 'nullable' : 'required',
                'string',
                'max:15',
                Rule::unique('estudiantes', 'dui'),
            ],
        ]);
        $data['dui'] = $request->input('dui');

        // 3) Datos del encargado si es menor
        if ($esMenor) {
            $request->validate([
                'encargado_nombre_completo' => 'required|string|max:200',
                'encargado_parentesco'      => 'nullable|string|max:50',
                'encargado_telefono'        => 'nullable|string|max:20',
            ]);
        }

        // 4) Verificar cupo del grupo dentro de la transacción (para evitar
        //    condiciones de carrera entre dos kioscos simultáneos).
        $estudiante = DB::transaction(function () use ($data, $request, $esMenor) {
            $grupo = Grupo::lockForUpdate()->findOrFail($data['grupo_id']);

            $disponibles = $grupo->cuposDisponibles();
            if (! is_null($disponibles) && $disponibles <= 0) {
                abort(422, 'El grupo seleccionado ya no tiene cupos disponibles.');
            }

            $estudiante = Estudiante::create([
                'nombres'          => $data['nombres'],
                'apellidos'        => $data['apellidos'],
                'sexo'             => $data['sexo'],
                'fecha_nacimiento' => $data['fecha_nacimiento'],
                'dui'              => $data['dui'],
                'correo'           => $data['correo'] ?? null,
                'telefono_celular' => $data['telefono_celular'] ?? null,
                'direccion'        => $data['direccion'] ?? null,
                'profesion_oficio' => $data['profesion_oficio'] ?? null,
                'nivel_estudio'    => $data['nivel_estudio'] ?? null,
                'activo'           => true,
            ]);

            if ($esMenor) {
                $estudiante->encargadoMenor()->create([
                    'nombre_completo' => $request->encargado_nombre_completo,
                    'parentesco'      => $request->encargado_parentesco,
                    'telefono'        => $request->encargado_telefono,
                ]);
            }

            // Inscripción automática con estado ACTIVA (coincide con
            // Grupo::cuposDisponibles()).
            $estudiante->inscripciones()->create([
                'grupo_id'          => $grupo->id,
                'estado'            => 'ACTIVA',
                'fecha_inscripcion' => now()->toDateString(),
            ]);

            return $estudiante;
        });

        return redirect()->route('kiosco.exito', $estudiante);
    }

    public function exito(Estudiante $estudiante)
    {
        $estudiante->load(['inscripciones.grupo.curso']);

        return view('kiosco.exito', compact('estudiante'));
    }
}