<x-app-layout title="Inscribir estudiante">
    <div
    class="max-w-lg rounded-xl bg-white p-6 shadow-sm border border-slate-100"
    x-data="{
        estudiantes: {{ Js::from(
            $estudiantes->map(fn ($e) => [
                'id' => $e->id,
                'label' =>
                    $e->nombre_completo .
                    ' — ' .
                    ($e->codigo_formatec ?? 'sin codigo') .
                    ($e->dui ? ' — DUI ' . $e->dui : '')
            ])->values()
        ) }},

        busqueda: '',
        estudianteId: '',
        estudianteLabel: '',
        abierto: false,

        get resultados() {
            if (this.busqueda.length < 2) {
                return [];
            }

            const q = this.busqueda.toLowerCase();

            return this.estudiantes
                .filter(e => e.label.toLowerCase().includes(q))
                .slice(0, 15);
        },

        elegir(e) {
            this.estudianteId = e.id;
            this.estudianteLabel = e.label;
            this.busqueda = e.label;
            this.abierto = false;
        }
    }"
>

    <form
        method="POST"
        action="{{ route('inscripciones.store') }}"
        class="space-y-4"
    >

        @csrf

        <input
            type="hidden"
            name="grupo_id"
            value="{{ $grupo->id }}"
        >

        <input
            type="hidden"
            name="estudiante_id"
            :value="estudianteId"
        >

        {{-- Estudiante --}}
        <div class="relative">

            <label
                for="estudiante"
                class="block text-sm font-medium text-slate-700"
            >
                Estudiante
            </label>

            <input
                id="estudiante"
                type="text"
                x-model="busqueda"
                @input="
                    abierto = true;
                    estudianteId = (busqueda === estudianteLabel)
                        ? estudianteId
                        : ''
                "
                @focus="abierto = true"
                @click.outside="abierto = false"
                placeholder="Escribe nombre, apellido, codigo o DUI..."
                autocomplete="off"
                class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm
                       focus:border-slate-500 focus:ring-slate-500"
            >

            {{-- Resultados --}}
            <div
                x-show="abierto && resultados.length > 0"
                x-cloak
                class="absolute z-20 mt-1 w-full max-h-64 overflow-y-auto
                       rounded-lg border border-slate-200 bg-white shadow-lg"
            >

                <template x-for="e in resultados" :key="e.id">

                    <button
                        type="button"
                        @click="elegir(e)"
                        class="block w-full px-3 py-2 text-left text-sm
                               hover:bg-slate-50"
                        x-text="e.label"
                    ></button>

                </template>

            </div>

            {{-- Sin resultados --}}
            <p
                x-show="abierto && busqueda.length >= 2 && resultados.length === 0"
                x-cloak
                class="mt-1 text-xs text-slate-400"
            >
                Sin resultados. Verifica el nombre o el codigo.
            </p>

            {{-- Menos de 2 caracteres --}}
            <p
                x-show="busqueda.length > 0 && busqueda.length < 2"
                class="mt-1 text-xs text-slate-400"
            >
                Escribe al menos 2 letras para buscar.
            </p>

            {{-- Error de validación --}}
            @error('estudiante_id')
                <p class="mt-1 text-xs text-red-600">
                    {{ $message }}
                </p>
            @enderror

            <p class="mt-1 text-xs text-slate-400">
                ¿El estudiante no existe todavía?

                <a
                    href="{{ route('estudiantes.create') }}"
                    class="underline text-slate-600 hover:text-slate-900"
                >
                    Regístralo primero
                </a>

                y luego vuelve aquí a inscribirlo.
            </p>

        </div>

        {{-- Fecha de inscripción --}}
        <div>

            <label
                for="fecha_inscripcion"
                class="block text-sm font-medium text-slate-700"
            >
                Fecha de inscripción
            </label>

            <input
                id="fecha_inscripcion"
                type="date"
                name="fecha_inscripcion"
                value="{{ old('fecha_inscripcion', now()->toDateString()) }}"
                required
                class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm
                       focus:border-slate-500 focus:ring-slate-500"
            >

        </div>

        {{-- Botón --}}
        <button
            type="submit"
            :disabled="!estudianteId"
            :class="estudianteId
                ? 'bg-slate-900 hover:bg-slate-800'
                : 'bg-slate-300 cursor-not-allowed'"
            class="rounded-lg px-4 py-2 text-sm font-semibold text-white transition"
        >
            Inscribir
        </button>

    </form>

</div>
</x-app-layout>
