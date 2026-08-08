<x-app-layout title="Profesores">
    <div class="flex items-center justify-between mb-4">
        <form method="GET" class="flex gap-2">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Buscar profesor..."
                   class="rounded-lg border border-slate-300 px-3 py-2 text-sm w-64">
            <button class="rounded-lg bg-slate-100 px-3 py-2 text-sm hover:bg-slate-200">Buscar</button>
        </form>
        <a href="{{ route('profesores.create') }}"
           class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
            + Nuevo profesor
        </a>
    </div>

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-left text-xs uppercase text-slate-500">
                <tr>
                    <th class="px-4 py-3">Nombre</th>
                    <th class="px-4 py-3">Especialidad</th>
                    <th class="px-4 py-3">Cuenta vinculada</th>
                    <th class="px-4 py-3">Estado</th>
                    <th class="px-4 py-3 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($profesores as $profesor)
                    <tr>
                        <td class="px-4 py-3 font-medium text-slate-900">{{ $profesor->nombre_completo }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $profesor->especialidad ?? '—' }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $profesor->user?->email ?? 'Sin cuenta' }}</td>
                        <td class="px-4 py-3">
                            <span class="rounded-full px-2 py-0.5 text-xs {{ $profesor->activo ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                                {{ $profesor->activo ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right space-x-3">
                            <a href="{{ route('profesores.edit', $profesor) }}" class="text-slate-600 hover:underline">Editar</a>
                            <form method="POST" action="{{ route('profesores.destroy', $profesor) }}" class="inline"
                                  onsubmit="return confirm('¿Eliminar este profesor?')">
                                @csrf @method('DELETE')
                                <button class="text-red-600 hover:underline">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-6 text-center text-slate-400">No hay profesores registrados.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $profesores->links() }}</div>
</x-app-layout>
