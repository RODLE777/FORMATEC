<x-app-layout title="Estudiantes">
    <div class="flex items-center justify-between mb-4">
        <form method="GET" class="flex gap-2">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Nombre o codigo FORMATEC..."
                   class="rounded-lg border border-slate-300 px-3 py-2 text-sm w-72">
            <button class="rounded-lg bg-slate-100 px-3 py-2 text-sm hover:bg-slate-200">Buscar</button>
        </form>
        @can('create', \App\Models\Estudiante::class)
            <a href="{{ route('estudiantes.create') }}"
               class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                + Nuevo estudiante
            </a>
        @endcan
    </div>

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-left text-xs uppercase text-slate-500">
                <tr>
                    <th class="px-4 py-3">Codigo</th>
                    <th class="px-4 py-3">Nombre</th>
                    <th class="px-4 py-3">Sexo</th>
                    <th class="px-4 py-3">Distrito</th>
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
                        <td class="px-4 py-3 text-slate-600">{{ $estudiante->distrito->nombre ?? '—' }}</td>
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
                    <tr><td colspan="6" class="px-4 py-6 text-center text-slate-400">No hay estudiantes registrados.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $estudiantes->links() }}</div>
</x-app-layout>
