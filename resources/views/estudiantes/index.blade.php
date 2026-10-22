<x-app-layout title="Estudiantes">
    
    {{-- PANEL DE FILTROS --}}
    <div class="mb-6 rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="mb-4 flex items-center justify-between">
            <h3 class="font-semibold text-slate-800">Buscador de Talento</h3>
            @can('create', \App\Models\Estudiante::class)
                <a href="{{ route('estudiantes.create') }}"
                   class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                    + Nuevo estudiante
                </a>
            @endcan
        </div>

        <form method="GET" action="{{ route('estudiantes.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
            
            {{-- Búsqueda de texto --}}
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">Nombre o código</label>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Ej: Rodrigo..."
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            </div>

            {{-- Filtro por Curso --}}
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">Curso / Habilidad</label>
                <select name="curso_id" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    <option value="">Cualquier curso</option>
                    @foreach ($cursos as $curso)
                        <option value="{{ $curso->id }}" {{ request('curso_id') == $curso->id ? 'selected' : '' }}>
                            {{ $curso->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Checkboxes de Restricciones --}}
            <div class="flex flex-col gap-2 justify-center h-full pt-4">
                <label class="flex items-center gap-2 text-sm text-slate-700 cursor-pointer">
                    <input type="checkbox" name="solo_graduados" value="1" {{ request('solo_graduados') ? 'checked' : '' }}
                           class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                    Solo Graduados (Finalizaron)
                </label>
                <label class="flex items-center gap-2 text-sm text-slate-700 cursor-pointer">
                    <input type="checkbox" name="filtro_mayor_18" value="1" {{ request('filtro_mayor_18') ? 'checked' : '' }}
                           class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                    Mayores de 18 años
                </label>
            </div>

            {{-- Botones --}}
            <div class="flex items-center gap-3">
                <button type="submit" class="w-full rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700 transition-colors">
                    Aplicar Filtros
                </button>
                @if(request()->anyFilled(['q', 'curso_id', 'solo_graduados', 'filtro_mayor_18']))
                    <a href="{{ route('estudiantes.index') }}" class="text-sm text-slate-500 hover:underline text-center w-full md:w-auto">
                        Limpiar
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- TABLA DE ESTUDIANTES --}}
    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-left text-xs uppercase text-slate-500">
                <tr>
                    <th class="px-4 py-3">Codigo</th>
                    <th class="px-4 py-3">Nombre</th>
                    <th class="px-4 py-3">Sexo</th>
                    <th class="px-4 py-3">Dirección</th> {{-- CAMBIO: Ahora es Dirección --}}
                    <th class="px-4 py-3">Estado</th>
                    <th class="px-4 py-3 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($estudiantes as $estudiante)
                    <tr>
                        <td class="px-4 py-3 font-mono text-xs text-slate-500">{{ $estudiante->codigo_formatec ?? '—' }}</td>
                        <td class="px-4 py-3 font-medium text-slate-900">
                            <a href="{{ route('estudiantes.show', $estudiante) }}" class="hover:underline">
                                {{ $estudiante->nombre_completo }}
                            </a>
                            @if ($estudiante->es_menor_edad)
                                <span class="ml-1 rounded-full bg-amber-50 px-2 py-0.5 text-[10px] text-amber-700">Menor</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-slate-600">{{ ucfirst(strtolower($estudiante->sexo)) }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ Str::limit($estudiante->direccion, 40) ?? '—' }}</td> {{-- CAMBIO: Muestra dirección --}}
                        <td class="px-4 py-3">
                            <span class="rounded-full px-2 py-0.5 text-xs {{ $estudiante->activo ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                                {{ $estudiante->activo ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right space-x-3">
                            <a href="{{ route('estudiantes.show', $estudiante) }}" class="text-slate-600 hover:underline">Ver</a>
                            @can('update', $estudiante)
                                <a href="{{ route('estudiantes.edit', $estudiante) }}" class="text-slate-600 hover:underline">Editar</a>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-slate-500">
                            No se encontraron estudiantes que coincidan con los filtros de búsqueda.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $estudiantes->links() }}</div>
</x-app-layout>