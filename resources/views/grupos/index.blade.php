
<x-app-layout title="Grupos">
    <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
        <form method="GET"
              action="{{ route('grupos.index') }}"
              class="flex flex-wrap gap-2">

            <select name="curso_id"
                    onchange="this.form.submit()"
                    class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
                <option value="">Todos mis cursos</option>

                @foreach($cursos as $curso)
                    <option value="{{ $curso->id }}"
                        @selected(request('curso_id') == $curso->id)>
                        {{ $curso->nombre }}
                    </option>
                @endforeach
            </select>

            <input type="number"
                   name="anio"
                   value="{{ request('anio') }}"
                   placeholder="Año"
                   class="w-24 rounded-lg border border-slate-300 px-3 py-2 text-sm">

            <button type="submit"
                    class="rounded-lg bg-slate-100 px-3 py-2 text-sm hover:bg-slate-200">
                Filtrar
            </button>

            @if(request()->filled('anio') || request()->filled('curso_id'))
                <a href="{{ route('grupos.index') }}"
                   class="rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-600 hover:bg-slate-50">
                    Limpiar
                </a>
            @endif
        </form>

        @can('create', \App\Models\Grupo::class)
            <a href="{{ route('grupos.create') }}"
               class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                + Nuevo grupo
            </a>
        @endcan
    </div>

    <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-left text-xs uppercase text-slate-500">
                <tr>
                    <th class="px-4 py-3">Grupo</th>
                    <th class="px-4 py-3">Curso</th>
                    <th class="px-4 py-3">Profesor</th>
                    <th class="px-4 py-3">Periodo</th>
                    <th class="px-4 py-3">Inscritos</th>
                    <th class="px-4 py-3">Estado</th>
                    <th class="px-4 py-3 text-right">Acciones</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-100">
                @forelse($grupos as $grupo)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 font-medium">
                            <a href="{{ route('grupos.show', $grupo) }}"
                               class="font-semibold text-slate-900 hover:text-emerald-700 hover:underline">
                                {{ $grupo->codigo_grupo }}
                            </a>
                        </td>

                        <td class="px-4 py-3 text-slate-600">
                            {{ $grupo->curso?->nombre ?? 'Sin curso' }}
                        </td>

                        <td class="px-4 py-3 text-slate-600">
                            {{ $grupo->profesor?->nombre_completo ?? 'Sin asignar' }}
                        </td>

                        <td class="px-4 py-3 text-slate-600">
                            {{ $grupo->mes }}/{{ $grupo->anio }}
                        </td>

                        <td class="px-4 py-3 text-slate-600">
                            {{ $grupo->inscripciones_count }}
                        </td>

                        <td class="px-4 py-3">
                            @php
                                $colorEstado = match($grupo->estado) {
                                    'EN_CURSO' => 'bg-blue-50 text-blue-700',
                                    'FINALIZADO' => 'bg-emerald-50 text-emerald-700',
                                    'CANCELADO' => 'bg-red-50 text-red-700',
                                    default => 'bg-slate-100 text-slate-600'
                                };
                            @endphp

                            <span class="rounded-full px-2 py-0.5 text-xs {{ $colorEstado }}">
                                {{ str_replace('_', ' ', $grupo->estado) }}
                            </span>
                        </td>

                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('grupos.show', $grupo) }}"
                               class="text-slate-600 hover:underline">
                                Ver
                            </a>

                            @can('update', $grupo)
                                <a href="{{ route('grupos.edit', $grupo) }}"
                                   class="ml-3 text-slate-600 hover:underline">
                                    Editar
                                </a>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7"
                            class="px-4 py-8 text-center text-slate-500">
                            No hay grupos que coincidan con los filtros.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $grupos->links() }}
    </div>
</x-app-layout>
