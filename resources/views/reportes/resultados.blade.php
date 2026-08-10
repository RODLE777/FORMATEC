<x-app-layout title="Resultados">
    <div class="mb-4 flex flex-wrap gap-2">
        @foreach (['GRADUADO' => 'Graduados', 'DESERTADO' => 'Desertados', 'REPROBADO' => 'Reprobados', 'EN_CURSO' => 'En curso'] as $valor => $etiqueta)
            <a href="{{ route('resultados.index', ['resultado' => $valor]) }}"
               class="rounded-lg px-4 py-2 text-sm font-semibold {{ $resultado === $valor ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                {{ $etiqueta }}
            </a>
        @endforeach
    </div>

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-left text-xs uppercase text-slate-500">
                <tr>
                    <th class="px-4 py-3">Estudiante</th>
                    <th class="px-4 py-3">Curso</th>
                    <th class="px-4 py-3">Grupo</th>
                    <th class="px-4 py-3">Nota final</th>
                    <th class="px-4 py-3">Fecha</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($inscripciones as $inscripcion)
                    <tr>
                        <td class="px-4 py-3 font-medium text-slate-900">
                            <a href="{{ route('estudiantes.show', $inscripcion->estudiante) }}" class="hover:underline">
                                {{ $inscripcion->estudiante->nombre_completo }}
                            </a>
                        </td>
                        <td class="px-4 py-3 text-slate-600">{{ $inscripcion->grupo->curso->nombre }}</td>
                        <td class="px-4 py-3 text-slate-600">
                            <a href="{{ route('grupos.show', $inscripcion->grupo) }}" class="hover:underline">{{ $inscripcion->grupo->codigo_grupo }}</a>
                        </td>
                        <td class="px-4 py-3 font-mono text-slate-700">{{ $inscripcion->nota_final ?? '—' }}</td>
                        <td class="px-4 py-3 text-slate-500">{{ $inscripcion->fecha_finalizacion?->format('d/m/Y') ?? '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-6 text-center text-slate-400">Sin resultados en esta categoria.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $inscripciones->links() }}</div>
</x-app-layout>
