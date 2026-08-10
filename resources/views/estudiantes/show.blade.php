<x-app-layout title="Ficha del estudiante">
    <div class="mb-4 flex items-start justify-between">
        <div class="flex items-center gap-4">
            @if ($estudiante->foto)
                <img src="{{ $estudiante->foto_url }}" class="h-16 w-16 rounded-full object-cover border border-slate-200">
            @else
                <div class="h-16 w-16 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 text-xl font-semibold">
                    {{ strtoupper(substr($estudiante->nombres, 0, 1).substr($estudiante->apellidos, 0, 1)) }}
                </div>
            @endif
            <div>
                <h2 class="text-xl font-bold text-slate-900">{{ $estudiante->nombre_completo }}</h2>
                <p class="text-sm text-slate-500 font-mono">{{ $estudiante->codigo_formatec }}</p>
            </div>
        </div>
        <div class="flex gap-2">
            @can('update', $estudiante)
                <a href="{{ route('estudiantes.edit', $estudiante) }}"
                   class="rounded-lg bg-slate-100 px-4 py-2 text-sm font-semibold hover:bg-slate-200">Editar ficha</a>
            @endcan
           
            @can('delete', $estudiante)
                <form method="POST" action="{{ route('estudiantes.destroy', $estudiante) }}"
                      onsubmit="return confirm('¿Eliminar a {{ $estudiante->nombre_completo }} permanentemente? Esta accion no se puede deshacer. Solo funciona si el estudiante no tiene inscripciones registradas.')">
                    @csrf @method('DELETE')
                    <button class="rounded-lg bg-red-50 px-4 py-2 text-sm font-semibold text-red-700 hover:bg-red-100">
                        Eliminar
                    </button>
                </form>
            @endcan
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-1 space-y-4">
            <div class="rounded-xl bg-white p-5 border border-slate-100 shadow-sm text-sm space-y-2">
                <p><span class="text-slate-500">Sexo:</span> {{ ucfirst(strtolower($estudiante->sexo)) }}</p>
                <p><span class="text-slate-500">Edad:</span> {{ $estudiante->edad }} años
                    @if ($estudiante->es_menor_edad)
                        <span class="rounded-full bg-amber-50 px-2 py-0.5 text-[10px] text-amber-700">Menor de edad</span>
                    @endif
                </p>
                <p><span class="text-slate-500">DUI:</span> {{ $estudiante->dui ?? '—' }}</p>
                <p><span class="text-slate-500">Correo:</span> {{ $estudiante->correo ?? '—' }}</p>
                <p><span class="text-slate-500">Celular:</span> {{ $estudiante->telefono_celular ?? '—' }}</p>
                <p><span class="text-slate-500">Direccion:</span> {{ $estudiante->direccion ?? '—' }}</p>
                <p><span class="text-slate-500">Distrito:</span> {{ $estudiante->distrito->nombre ?? '—' }}, {{ $estudiante->municipio->nombre ?? '' }}</p>
            </div>

            @if ($estudiante->es_menor_edad)
                <div class="rounded-xl bg-amber-50 border border-amber-200 p-5 text-sm space-y-1">
                    <p class="font-semibold text-amber-800">Encargado</p>
                    <p>{{ $estudiante->encargadoMenor?->nombre_completo ?? 'No registrado' }}</p>
                    <p class="text-amber-700">{{ $estudiante->encargadoMenor?->parentesco }} · {{ $estudiante->encargadoMenor?->telefono }}</p>
                </div>
            @endif
        </div>

        <div class="lg:col-span-2">
            <h3 class="font-semibold text-slate-900 mb-2">Historial de cursos</h3>
            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-xs uppercase text-slate-500">
                        <tr>
                            <th class="px-4 py-3">Curso / grupo</th>
                            <th class="px-4 py-3">Periodo</th>
                            <th class="px-4 py-3">Resultado</th>
                            <th class="px-4 py-3">Nota final</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($estudiante->inscripciones as $inscripcion)
                            <tr>
                                <td class="px-4 py-3 font-medium text-slate-900">
                                    <a href="{{ route('grupos.show', $inscripcion->grupo) }}" class="hover:underline">
                                        {{ $inscripcion->grupo->curso->nombre }}
                                        <span class="text-slate-400 text-xs">({{ $inscripcion->grupo->codigo_grupo }})</span>
                                    </a>
                                </td>
                                <td class="px-4 py-3 text-slate-600">{{ $inscripcion->grupo->mes }}/{{ $inscripcion->grupo->anio }}</td>
                                <td class="px-4 py-3">
                                    <span class="rounded-full px-2 py-0.5 text-xs
                                        {{ $inscripcion->resultado_final === 'GRADUADO' ? 'bg-emerald-50 text-emerald-700' : '' }}
                                        {{ $inscripcion->resultado_final === 'DESERTADO' ? 'bg-red-50 text-red-700' : '' }}
                                        {{ $inscripcion->resultado_final === 'REPROBADO' ? 'bg-red-50 text-red-700' : '' }}
                                        {{ $inscripcion->resultado_final === 'EN_CURSO' ? 'bg-slate-100 text-slate-600' : '' }}">
                                        {{ $inscripcion->resultado_final }}
                                    </span>
                                </td>
                                {{-- nota_final se muestra solo, jamas se edita aqui: la calcula el trigger --}}
                                <td class="px-4 py-3 font-mono text-slate-700">{{ $inscripcion->nota_final ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-4 py-6 text-center text-slate-400">Sin cursos registrados aun.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
