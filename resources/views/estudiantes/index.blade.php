
<x-app-layout title="Estudiantes">
    @php
        $esAdmin = in_array(
            auth()->user()->rol,
            ['ROOT', 'ADMINISTRADOR'],
            true
        );
    @endphp

    <div class="mb-6">
        <h2 class="text-xl font-bold text-slate-900">
            {{ $esAdmin ? 'Estudiantes registrados' : 'Mis estudiantes' }}
        </h2>

        <p class="mt-1 text-sm text-slate-500">
            {{ $esAdmin
                ? 'Consulta y administra las fichas de estudiantes.'
                : 'Consulta los estudiantes pertenecientes a tus grupos asignados.' }}
        </p>
    </div>

    <div class="mb-6 rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
            <h3 class="font-semibold text-slate-800">
                Buscador de estudiantes
            </h3>

            @if($esAdmin)
                <a href="{{ route('estudiantes.create') }}"
                   class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                    + Nuevo estudiante
                </a>
            @endif
        </div>

        <form method="GET"
              action="{{ route('estudiantes.index') }}"
              class="grid grid-cols-1 items-end gap-4 md:grid-cols-2 lg:grid-cols-4">

            <div>
                <label class="mb-1 block text-xs font-medium text-slate-500">
                    Nombre o código
                </label>

                <input type="text"
                       name="q"
                       value="{{ request('q') }}"
                       placeholder="Buscar estudiante..."
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            </div>

            <div>
                <label class="mb-1 block text-xs font-medium text-slate-500">
                    {{ $esAdmin ? 'Curso / Habilidad' : 'Mis cursos' }}
                </label>

                <select name="curso_id"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    <option value="">Todos los disponibles</option>

                    @foreach($cursos as $curso)
                        <option value="{{ $curso->id }}"
                            @selected(request('curso_id') == $curso->id)>
                            {{ $curso->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex flex-col justify-center gap-2">
                <label class="flex cursor-pointer items-center gap-2 text-sm text-slate-700">
                    <input type="checkbox"
                           name="solo_graduados"
                           value="1"
                           @checked(request('solo_graduados'))
                           class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                    Solo graduados
                </label>

                <label class="flex cursor-pointer items-center gap-2 text-sm text-slate-700">
                    <input type="checkbox"
                           name="filtro_mayor_18"
                           value="1"
                           @checked(request('filtro_mayor_18'))
                           class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                    Mayores de 18 años
                </label>
            </div>

            <div class="flex items-center gap-3">
                <button type="submit"
                        class="flex-1 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">
                    Aplicar filtros
                </button>

                <a href="{{ route('estudiantes.index') }}"
                   class="rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-600 hover:bg-slate-50">
                    Limpiar
                </a>
            </div>
        </form>
    </div>

    <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-left text-xs uppercase text-slate-500">
                <tr>
                    <th class="px-4 py-3">Código</th>
                    <th class="px-4 py-3">Nombre</th>
                    <th class="px-4 py-3">Sexo</th>

                    @if($esAdmin)
                        <th class="px-4 py-3">Dirección</th>
                    @endif

                    <th class="px-4 py-3">Estado</th>
                    <th class="px-4 py-3 text-right">Acciones</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-100">
                @forelse($estudiantes as $estudiante)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 font-mono text-xs text-slate-500">
                            {{ $estudiante->codigo_formatec ?? '—' }}
                        </td>

                        <td class="px-4 py-3">
                            <a href="{{ route('estudiantes.show', $estudiante) }}"
                               class="font-semibold text-slate-900 hover:text-emerald-700">
                                {{ $estudiante->nombre_completo }}
                            </a>

                            @if($estudiante->es_menor_edad)
                                <span class="ml-1 rounded-full bg-amber-50 px-2 py-0.5 text-[10px] text-amber-700">
                                    Menor
                                </span>
                            @endif
                        </td>

                        <td class="px-4 py-3 text-slate-600">
                            {{ ucfirst(strtolower($estudiante->sexo)) }}
                        </td>

                        @if($esAdmin)
                            <td class="px-4 py-3 text-slate-600">
                                {{ \Illuminate\Support\Str::limit($estudiante->direccion ?? '—', 40) }}
                            </td>
                        @endif

                        <td class="px-4 py-3">
                            <span @class([
                                'rounded-full px-2 py-0.5 text-xs',
                                'bg-emerald-50 text-emerald-700' => $estudiante->activo,
                                'bg-slate-100 text-slate-500' => !$estudiante->activo,
                            ])>
                                {{ $estudiante->activo ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>

                        <td class="px-4 py-3 text-right">
                            <div class="flex justify-end gap-3">
                                <a href="{{ route('estudiantes.show', $estudiante) }}"
                                   class="font-medium text-emerald-700 hover:underline">
                                    Ver
                                </a>

                                @if($esAdmin)
                                    <a href="{{ route('estudiantes.edit', $estudiante) }}"
                                       class="text-slate-600 hover:underline">
                                        Editar
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $esAdmin ? 6 : 5 }}"
                            class="px-4 py-10 text-center text-slate-500">
                            No se encontraron estudiantes que coincidan con los filtros.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-5">
        {{ $estudiantes->links() }}
    </div>
</x-app-layout>
