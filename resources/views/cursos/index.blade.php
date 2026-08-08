<x-app-layout title="Cursos">
    <div class="flex items-center justify-between mb-4">
        <form method="GET" class="flex gap-2">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Buscar curso..."
                   class="rounded-lg border border-slate-300 px-3 py-2 text-sm w-64">
            <button class="rounded-lg bg-slate-100 px-3 py-2 text-sm hover:bg-slate-200">Buscar</button>
        </form>
        <a href="{{ route('cursos.create') }}"
           class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
            + Nuevo curso
        </a>
    </div>

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-left text-xs uppercase text-slate-500">
                <tr>
                    <th class="px-4 py-3">Nombre</th>
                    <th class="px-4 py-3">Grupos</th>
                    <th class="px-4 py-3">Estado</th>
                    <th class="px-4 py-3 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($cursos as $curso)
                    <tr>
                        <td class="px-4 py-3 font-medium text-slate-900">{{ $curso->nombre }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $curso->grupos_count }}</td>
                        <td class="px-4 py-3">
                            <span class="rounded-full px-2 py-0.5 text-xs {{ $curso->activo ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                                {{ $curso->activo ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right space-x-3">
                            <a href="{{ route('cursos.edit', $curso) }}" class="text-slate-600 hover:underline">Editar</a>
                            <form method="POST" action="{{ route('cursos.destroy', $curso) }}" class="inline"
                                  onsubmit="return confirm('¿Eliminar este curso?')">
                                @csrf @method('DELETE')
                                <button class="text-red-600 hover:underline">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-4 py-6 text-center text-slate-400">No hay cursos registrados.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $cursos->links() }}</div>
</x-app-layout>
