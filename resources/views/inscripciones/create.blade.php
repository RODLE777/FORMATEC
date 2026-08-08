<x-app-layout title="Inscribir estudiante">
    <div class="mb-4">
        <a href="{{ route('grupos.show', ['grupo' => $grupo, 'tab' => 'estudiantes']) }}" class="text-sm text-slate-500 hover:underline">
            ← Volver al grupo
        </a>
        <h2 class="text-xl font-bold text-slate-900 mt-1">Inscribir estudiante en {{ $grupo->codigo_grupo }}</h2>
        <p class="text-sm text-slate-500">{{ $grupo->curso->nombre }}</p>
    </div>

    <div class="max-w-lg rounded-xl bg-white p-6 shadow-sm border border-slate-100">
        <form method="POST" action="{{ route('inscripciones.store') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="grupo_id" value="{{ $grupo->id }}">

            <div>
                <label class="block text-sm font-medium text-slate-700">Estudiante</label>
                <select name="estudiante_id" required class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    <option value="">— Selecciona —</option>
                    @foreach ($estudiantes as $estudiante)
                        <option value="{{ $estudiante->id }}">{{ $estudiante->nombre_completo }} ({{ $estudiante->codigo_formatec }})</option>
                    @endforeach
                </select>
                @error('estudiante_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                <p class="mt-1 text-xs text-slate-400">
                    ¿El estudiante no existe todavia?
                    <a href="{{ route('estudiantes.create') }}" class="underline">Registralo primero</a> y luego vuelve aqui a inscribirlo.
                </p>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700">Fecha de inscripcion</label>
                <input type="date" name="fecha_inscripcion" value="{{ old('fecha_inscripcion', now()->toDateString()) }}" required
                       class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            </div>

            <button class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                Inscribir
            </button>
        </form>
    </div>
</x-app-layout>
