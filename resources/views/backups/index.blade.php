<x-app-layout title="Backups">
    <div class="mb-6 flex items-center justify-between rounded-xl bg-white p-6 shadow-sm border border-slate-100">
        <div>
            <h3 class="font-semibold text-slate-900">Respaldo de base de datos</h3>
            <p class="text-sm text-slate-500 mt-1">
                Genera un volcado completo (estructura, datos, triggers y vistas) via mysqldump.
            </p>
        </div>
        <form method="POST" action="{{ route('backups.store') }}">
            @csrf
            <button class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                Generar backup ahora
            </button>
        </form>
    </div>

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-left text-xs uppercase text-slate-500">
                <tr>
                    <th class="px-4 py-3">Archivo</th>
                    <th class="px-4 py-3">Tipo</th>
                    <th class="px-4 py-3">Tamaño</th>
                    <th class="px-4 py-3">Generado por</th>
                    <th class="px-4 py-3">Fecha</th>
                    <th class="px-4 py-3">Estado</th>
                    <th class="px-4 py-3 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($backups as $backup)
                    <tr>
                        <td class="px-4 py-3 font-mono text-xs">{{ $backup->nombre_archivo }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $backup->tipo }}</td>
                        <td class="px-4 py-3 text-slate-600">
                            {{ $backup->tamano_bytes ? round($backup->tamano_bytes / 1024, 1).' KB' : '—' }}
                        </td>
                        <td class="px-4 py-3 text-slate-600">{{ $backup->usuario->name }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $backup->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3">
                            <span class="rounded-full px-2 py-0.5 text-xs {{ $backup->estado === 'COMPLETADO' ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700' }}">
                                {{ $backup->estado }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            @if ($backup->estado === 'COMPLETADO')
                                <a href="{{ route('backups.download', $backup) }}" class="text-slate-600 hover:underline">Descargar</a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-4 py-6 text-center text-slate-400">Aun no se ha generado ningun backup.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $backups->links() }}</div>

    <p class="mt-4 text-xs text-slate-400">
        La restauracion de un backup es una operacion delicada (sobrescribe la base de datos actual)
        y se realiza manualmente por el equipo tecnico, no desde esta pantalla, para evitar perdidas
        accidentales de informacion.
    </p>
</x-app-layout>
