<x-app-layout title="Evaluaciones">
    <div class="max-w-3xl mx-auto">
        <a href="{{ route('grupos.show', ['grupo' => $grupo, 'tab' => 'estudiantes']) }}"
           class="text-sm text-slate-500 hover:underline">← Volver al grupo</a>

        <h2 class="mt-2 text-xl font-bold text-slate-900">{{ $inscripcion->estudiante->nombre_completo }}</h2>
        <p class="text-sm text-slate-500">
            {{ $grupo->curso->nombre }} · {{ $grupo->codigo_grupo }}
        </p>

        <div class="mt-4 rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-600">
            Nota final actual:
            @if ($inscripcion->nota_final !== null)
                <strong class="font-mono text-slate-900">{{ $inscripcion->nota_final }}</strong>
            @else
                <strong>— (aún sin evaluaciones)</strong>
            @endif
            — se calcula automáticamente al guardar notas, no se edita manualmente.
        </div>

        <form method="POST" action="{{ route('grupos.evaluaciones.guardar', [$grupo, $inscripcion]) }}"
              class="mt-6 space-y-4">
            @csrf

            @foreach ($config as $i => $ev)
                @php
                    $numero = $i + 1;
                    $actual = $inscripcion->evaluaciones->firstWhere('numero_evaluacion', $numero);
                @endphp
                <div class="rounded-xl border border-slate-200 bg-white p-4">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-sm font-semibold text-slate-700">
                            {{ $ev['nombre'] }}
                        </p>
                        <span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs font-semibold text-slate-600">
                            {{ number_format($ev['porcentaje'], 2) }}%
                        </span>
                    </div>
                    <label class="block text-xs font-medium text-slate-500">Nota (0-10)</label>
                    <input type="number"
                           name="nota[{{ $numero }}]"
                           value="{{ old('nota.'.$numero, $actual?->nota) }}"
                           step="0.01" min="0" max="10"
                           class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                </div>
            @endforeach

            <button class="rounded-lg bg-slate-900 px-5 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                Guardar evaluaciones
            </button>
        </form>
    </div>
</x-app-layout>