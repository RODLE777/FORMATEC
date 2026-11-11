
<x-kiosco-layout>
    <div class="max-w-2xl mx-auto bg-white border
                border-slate-200 shadow-lg rounded-2xl
                p-9 text-center">

        <div class="text-5xl mb-4">
            ✅
        </div>

        <h1 class="text-2xl font-bold">
            ¡Inscripción registrada!
        </h1>

        <p class="text-slate-500 mt-3">
            Tu inscripción se guardó correctamente.
        </p>

        <div class="bg-slate-50 border border-slate-200
                    rounded-xl p-5 mt-6">

            <p class="text-xs uppercase tracking-widest
                      text-slate-500">
                Tu carnet FORMATEC
            </p>

            <p class="text-2xl font-bold mt-2">
                {{ $estudiante->codigo_formatec }}
            </p>
        </div>

        <p class="text-sm text-slate-500 mt-4">
            Conserva este código para futuras inscripciones.
        </p>

        <a
            class="inline-block bg-emerald-700 text-white
                   font-bold px-6 py-3 rounded-xl mt-7"
            href="{{ route('kiosco.index') }}"
        >
            Registrar otra persona
        </a>
    </div>
</x-kiosco-layout>
