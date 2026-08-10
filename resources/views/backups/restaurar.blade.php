<x-app-layout title="Restaurar backup">
    <div class="max-w-lg mx-auto">
        <div class="rounded-xl bg-red-50 border border-red-200 p-5 mb-4">
            <p class="font-semibold text-red-800">⚠ Esta accion es irreversible</p>
            <p class="text-sm text-red-700 mt-1">
                Vas a reemplazar TODA la base de datos actual con el contenido de
                <strong>{{ $backup->nombre_archivo }}</strong> (generado el {{ $backup->created_at->format('d/m/Y H:i') }}).
                Cualquier dato registrado despues de ese backup se perdera.
            </p>
            <p class="text-sm text-red-700 mt-2">
                El sistema generara automaticamente un backup de seguridad del estado actual
                antes de restaurar, por si necesitas revertir esta operacion.
            </p>
        </div>

        <form method="POST" action="{{ route('backups.restore', $backup) }}" class="rounded-xl bg-white p-6 border border-slate-100 shadow-sm space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-slate-700">
                    Escribe <span class="font-mono font-bold">RESTAURAR</span> para confirmar
                </label>
                <input type="text" name="confirmacion" required autocomplete="off"
                       class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                @error('confirmacion') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div class="flex gap-3">
                <button class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">
                    Restaurar ahora
                </button>
                <a href="{{ route('backups.index') }}" class="rounded-lg bg-slate-100 px-4 py-2 text-sm font-semibold hover:bg-slate-200">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</x-app-layout>
