<x-app-layout title="Grupos">
    <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
        <form method="GET" class="flex gap-2">
            <select name="curso_id" onchange="this.form.submit()" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
                <option value="">Todos los cursos</option>
                @foreach ($cursos as $c)
                    <option value="{{ $c->id }}" {{ request('curso_id') == $c->id ? 'selected' : '' }}>{{ $c->nombre }}</option>
                @endforeach
            </select>
            <input type="number" name="anio" value="{{ request('anio') }}" placeholder="Año"
                   class="w-24 rounded-lg border border-slate-300 px-3 py-2 text-sm">
            <button class="rounded-lg bg-slate-100 px-3 py-2 text-sm hover:bg-slate-200">Filtrar</button>
        </form>
        @can('create', \App\Models\Grupo::class)
            <a href="{{ route('grupos.create') }}"
               class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                + Nuevo grupo
            </a>
        @endcan
    </div>

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white">
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
                @forelse ($grupos as $grupo)
                    <tr>
                        <td class="px-4 py-3 font-medium text-slate-900">
                            <a href="{{ route('grupos.show', $grupo) }}" class="hover:underline">{{ $grupo->codigo_grupo }}</a>
                        </td>
                        <td class="px-4 py-3 text-slate-600">{{ $grupo->curso->nombre }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $grupo->profesor?->nombre_completo ?? 'Sin asignar' }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $grupo->mes }}/{{ $grupo->anio }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $grupo->inscripciones_count }}</td>
                        <td class="px-4 py-3">
                            <span class="rounded-full px-2 py-0.5 text-xs
                                {{ $grupo->estado === 'EN_CURSO' ? 'bg-blue-50 text-blue-700' : '' }}
                                {{ $grupo->estado === 'FINALIZADO' ? 'bg-emerald-50 text-emerald-700' : '' }}
                                {{ $grupo->estado === 'CANCELADO' ? 'bg-red-50 text-red-700' : '' }}
                                {{ $grupo->estado === 'PLANIFICADO' ? 'bg-slate-100 text-slate-600' : '' }}">
                                {{ $grupo->estado }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right space-x-3">
                            <a href="{{ route('grupos.show', $grupo) }}" class="text-slate-600 hover:underline">Ver</a>
                            @can('update', $grupo)
                                <a href="{{ route('grupos.edit', $grupo) }}" class="text-slate-600 hover:underline">Editar</a>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-4 py-6 text-center text-slate-400">No hay grupos registrados.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $grupos->links() }}</div>
</x-app-layout>
