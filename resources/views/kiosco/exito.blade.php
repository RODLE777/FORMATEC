<x-kiosco-layout>
    <div class="bg-white rounded-2xl shadow-lg border border-slate-200 p-8 text-center">
        <div class="mx-auto h-16 w-16 rounded-full bg-emerald-100 flex items-center justify-center mb-4">
            <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-slate-900">¡Registro exitoso!</h1>
        <p class="mt-2 text-slate-600">Tu inscripción fue registrada correctamente.</p>

        <div class="mt-6 inline-block rounded-xl bg-slate-50 border border-slate-200 px-6 py-4">
            <p class="text-xs uppercase tracking-widest text-slate-500">Tu código</p>
            <p class="text-2xl font-bold text-slate-900">{{ $estudiante->codigo_formatec }}</p>
        </div>

        @if ($estudiante->inscripciones->isNotEmpty())
            <div class="mt-6 text-sm text-slate-700">
                <p class="font-semibold">Curso inscrito:</p>
                @foreach ($estudiante->inscripciones as $ins)
                    <p>{{ $ins->grupo->curso->nombre ?? 'Curso #'.$ins->grupo_id }}</p>
                @endforeach
            </div>
        @endif

      

        <a href="{{ route('kiosco.index') }}"
           class="mt-8 inline-block rounded-lg bg-slate-900 px-6 py-2 text-sm font-semibold text-white hover:bg-slate-800">
            Registrar otra persona
        </a>
    </div>
</x-kiosco-layout>