<x-app-layout title="Importacion masiva">
    <div class="mb-6 max-w-xl rounded-xl bg-white p-6 shadow-sm border border-slate-100">
        <h3 class="font-semibold text-slate-900 mb-1">Importar estudiantes desde Excel</h3>
        <p class="text-sm text-slate-500 mb-4">
            Columnas esperadas en la fila 1: nombres, apellidos, sexo (MASCULINO/FEMENINO),
            fecha_nacimiento, dui, correo, telefono_celular.
        </p>
        <form method="POST" action="{{ route('importaciones.store') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <input type="hidden" name="tipo" value="ESTUDIANTES">
            <input type="file" name="archivo" accept=".xlsx,.xls,.csv" required
                   class="block w-full text-sm text-slate-600 file:mr-3 file:rounded-lg file:border-0 file:bg-slate-100 file:px-3 file:py-2 file:text-sm">
            @error('archivo') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
            <button class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                Importar
            </button>
        </form>
    </div>

    <h3 class="font-semibold text-slate-900 mb-2">Historial de importaciones</h3>
    <div class="space-y-3">
        @forelse ($importaciones as $importacion)
            <div class="rounded-xl bg-white p-4 border border-slate-100 shadow-sm text-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="font-medium text-slate-900">{{ $importacion->archivo_nombre }}</span>
                        <span class="text-slate-400"> · {{ $importacion->created_at->format('d/m/Y H:i') }} · {{ $importacion->usuario->name }}</span>
                    </div>
                    <span class="rounded-full px-2 py-0.5 text-xs
                        {{ $importacion->estado === 'COMPLETADO' ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700' }}">
                        {{ $importacion->estado }}
                    </span>
                </div>
                <p class="mt-1 text-slate-600">
                    {{ $importacion->filas_importadas }} de {{ $importacion->total_filas }} filas importadas
                    @if ($importacion->filas_error > 0)
                        · <span class="text-red-600">{{ $importacion->filas_error }} con error</span>
                    @endif
                </p>
                @if ($importacion->detalle_errores)
                    <details class="mt-2">
                        <summary class="cursor-pointer text-xs text-slate-500">Ver detalle de errores</summary>
                        <ul class="mt-1 text-xs text-red-600 list-disc list-inside">
                            @foreach ($importacion->detalle_errores as $err)
                                <li>Fila {{ $err['fila'] }}: {{ $err['error'] }}</li>
                            @endforeach
                        </ul>
                    </details>
                @endif
            </div>
        @empty
            <p class="text-slate-400 text-sm">Aun no se ha importado nada.</p>
        @endforelse
    </div>

    <div class="mt-4">{{ $importaciones->links() }}</div>
</x-app-layout>
