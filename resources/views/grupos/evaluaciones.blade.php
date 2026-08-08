<x-app-layout title="Evaluaciones">
    <div class="mb-4">
        <a href="{{ route('grupos.show', ['grupo' => $grupo, 'tab' => 'estudiantes']) }}" class="text-sm text-slate-500 hover:underline">
            ← Volver al grupo
        </a>
        <h2 class="text-xl font-bold text-slate-900 mt-1">{{ $inscripcion->estudiante->nombre_completo }}</h2>
        <p class="text-sm text-slate-500">{{ $grupo->curso->nombre }} · {{ $grupo->codigo_grupo }}</p>
    </div>

    <div class="mb-4 rounded-xl bg-slate-50 border border-slate-200 px-4 py-3 text-sm">
        Nota final actual:
        <span class="font-mono font-semibold">{{ $inscripcion->nota_final ?? '— (aun sin evaluaciones)' }}</span>
        <span class="text-slate-400"> — se calcula automaticamente al guardar notas, no se edita manualmente.</span>
    </div>

    <form method="POST" action="{{ route('grupos.evaluaciones.guardar', [$grupo, $inscripcion]) }}" class="max-w-xl space-y-4">
        @csrf
        @for ($n = 1; $n <= $grupo->numero_evaluaciones; $n++)
            @php $evaluacion = $inscripcion->evaluaciones->firstWhere('numero_evaluacion', $n); @endphp
            <div class="rounded-xl border border-slate-200 bg-white p-4 grid grid-cols-3 gap-3 items-end">
                <div class="col-span-2">
                    <label class="block text-xs font-medium text-slate-500">Nombre evaluacion {{ $n }}</label>
                    <input type="text" name="nombre_evaluacion[{{ $n }}]"
                           value="{{ $evaluacion->nombre_evaluacion ?? "Evaluacion {$n}" }}"
                           class="mt-1 w-full rounded-lg border border-slate-300 px-2 py-1.5 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500">Nota (0-10)</label>
                    <input type="number" step="0.01" min="0" max="10" name="nota[{{ $n }}]"
                           value="{{ $evaluacion->nota ?? '' }}"
                           class="mt-1 w-full rounded-lg border border-slate-300 px-2 py-1.5 text-sm">
                </div>
            </div>
        @endfor

        <button class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
            Guardar evaluaciones
        </button>
    </form>
</x-app-layout>
