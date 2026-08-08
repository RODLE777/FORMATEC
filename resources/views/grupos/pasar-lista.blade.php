<x-app-layout title="Pasar lista">
    <div class="mb-4">
        <a href="{{ route('grupos.show', ['grupo' => $grupo, 'tab' => 'sesiones']) }}" class="text-sm text-slate-500 hover:underline">
            ← Volver al grupo
        </a>
        <h2 class="text-xl font-bold text-slate-900 mt-1">
            Sesion {{ $sesion->numero_sesion }} — {{ $sesion->fecha->format('d/m/Y') }}
        </h2>
        <p class="text-sm text-slate-500">{{ $grupo->curso->nombre }} · {{ $grupo->codigo_grupo }} @if($sesion->tema) · {{ $sesion->tema }} @endif</p>
    </div>

    <form method="POST" action="{{ route('grupos.sesiones.asistencia.guardar', [$grupo, $sesion]) }}">
        @csrf
        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs uppercase text-slate-500">
                    <tr>
                        <th class="px-4 py-3">Estudiante</th>
                        <th class="px-4 py-3 text-center">Asistio</th>
                        <th class="px-4 py-3">Observacion</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($inscripciones as $inscripcion)
                        @php $registro = $inscripcion->asistencias->first(); @endphp
                        <tr>
                            <td class="px-4 py-3 font-medium text-slate-900">{{ $inscripcion->estudiante->nombre_completo }}</td>
                            <td class="px-4 py-3 text-center">
                                <input type="checkbox" name="asistencia[{{ $inscripcion->id }}]" value="1"
                                       {{ $registro?->asistio ? 'checked' : '' }} class="h-4 w-4">
                            </td>
                            <td class="px-4 py-3">
                                <input type="text" name="observacion[{{ $inscripcion->id }}]"
                                       value="{{ $registro?->observacion }}"
                                       class="w-full rounded-lg border border-slate-300 px-2 py-1 text-sm">
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="px-4 py-6 text-center text-slate-400">No hay estudiantes activos en este grupo.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <button class="mt-4 rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
            Guardar asistencia
        </button>
    </form>
</x-app-layout>
