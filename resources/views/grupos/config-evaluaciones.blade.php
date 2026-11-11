<x-app-layout :title="'Config. evaluaciones · '.$grupo->codigo_grupo">
    @php
        $usuario = auth()->user();

        $esAdmin = in_array(
            $usuario->rol,
            ['ROOT', 'ADMINISTRADOR'],
            true
        );

        $esProfesorAsignado = $usuario->esProfesor()
            && $usuario->profesor
            && (int) $grupo->profesor_id === (int) $usuario->profesor->id;

        $puedeEditar = $esAdmin || (
            $esProfesorAsignado && $grupo->estado === 'EN_CURSO'
        );
    @endphp

    <div class="max-w-4xl mx-auto"
         x-data="configEvals(@js(old('evaluaciones', $config)))">

        <div class="mb-4">
            <a href="{{ route('grupos.show', ['grupo' => $grupo, 'tab' => 'config-evaluaciones']) }}"
               class="text-sm text-slate-500 hover:underline">
                ← Volver al grupo
            </a>

            <h2 class="mt-2 text-xl font-bold text-slate-900">
                Configuración de evaluaciones
            </h2>

            <p class="text-sm text-slate-500">
                {{ $grupo->curso->nombre }} — {{ $grupo->codigo_grupo }}
                · Los porcentajes deben sumar exactamente 100%.
            </p>
        </div>

        @if(session('error'))
            <div class="mb-4 rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-800">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-4 rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-800">
                <ul class="list-disc pl-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @unless($puedeEditar)
            <div class="mb-4 rounded-lg bg-amber-50 border border-amber-200 px-4 py-3 text-sm text-amber-800">
                Puedes consultar la configuración, pero no modificarla.
            </div>
        @endunless

        <form method="POST"
              action="{{ route('grupos.config-evaluaciones.guardar', $grupo) }}"
              class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">

            @csrf
            @method('PUT')

            <div class="flex items-center justify-between mb-4 gap-3">
                <p class="text-sm font-semibold text-slate-700">
                    Evaluaciones del grupo
                    (<span x-text="evaluaciones.length"></span>)
                </p>

                @if($puedeEditar)
                    <button type="button"
                            @click="repartirIgual()"
                            class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                        Repartir en partes iguales
                    </button>
                @endif
            </div>

            <div class="space-y-3">
                <template x-for="(ev, i) in evaluaciones" :key="i">
                    <div class="flex items-end gap-3 rounded-lg border border-slate-200 p-3">

                        <div class="flex-1">
                            <label class="block text-xs font-medium text-slate-500">
                                Nombre
                            </label>

                            <input type="text"
                                   :name="'evaluaciones['+i+'][nombre]'"
                                   x-model="ev.nombre"
                                   required
                                   maxlength="100"
                                   @disabled(!$puedeEditar)
                                   class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm disabled:bg-slate-50">
                        </div>

                        <div class="w-32">
                            <label class="block text-xs font-medium text-slate-500">
                                Porcentaje
                            </label>

                            <div class="relative mt-1">
                                <input type="number"
                                       :name="'evaluaciones['+i+'][porcentaje]'"
                                       x-model.number="ev.porcentaje"
                                       @input="
                                           if (
                                               $event.target.value !== '' &&
                                               Number($event.target.value) < 0
                                           ) {
                                               ev.porcentaje = 0;
                                               $event.target.value = 0;
                                           }
                                       "
                                       @blur="
                                           if (
                                               ev.porcentaje === '' ||
                                               !Number.isFinite(Number(ev.porcentaje)) ||
                                               Number(ev.porcentaje) < 0
                                           ) {
                                               ev.porcentaje = 0;
                                           }
                                       "
                                       step="0.01"
                                       min="0"
                                       max="100"
                                       required
                                       @disabled(!$puedeEditar)
                                       class="w-full rounded-lg border border-slate-300 pl-3 pr-8 py-2 text-sm text-right disabled:bg-slate-50">

                                <span class="absolute inset-y-0 right-3 flex items-center text-sm text-slate-400">
                                    %
                                </span>
                            </div>
                        </div>

                        @if($puedeEditar)
                            <button type="button"
                                    @click="quitar(i)"
                                    :disabled="evaluaciones.length <= 1"
                                    class="rounded-lg border border-slate-200 px-3 py-2 text-sm text-red-600 hover:bg-red-50 disabled:opacity-40 disabled:cursor-not-allowed">
                                ✕
                            </button>
                        @endif
                    </div>
                </template>
            </div>

            @if($puedeEditar)
                <button type="button"
                        @click="agregar()"
                        :disabled="evaluaciones.length >= 50"
                        class="mt-3 rounded-lg border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 disabled:opacity-40">
                    + Agregar evaluación
                </button>
            @endif

            {{-- RESUMEN DE PORCENTAJES --}}
            <div class="mt-6 rounded-xl p-4 border"
                 :class="esValido
                    ? 'bg-emerald-50 border-emerald-200'
                    : 'bg-red-50 border-red-200'">

                <p class="text-sm font-semibold"
                   :class="esValido ? 'text-emerald-800' : 'text-red-800'">

                    Suma actual:
                    <span x-text="sumaFormateada"></span>%
                </p>

                <p class="text-xs mt-1"
                   :class="esValido ? 'text-emerald-700' : 'text-red-700'">

                    <span x-show="esValido">
                        ✓ Los porcentajes suman exactamente 100%.
                    </span>

                    <span x-show="!esValido && suma < 100">
                        Faltan
                        <span x-text="(100 - suma).toFixed(2)"></span>%
                    </span>

                    <span x-show="!esValido && suma > 100">
                        Excede en
                        <span x-text="(suma - 100).toFixed(2)"></span>%
                    </span>

                    <span x-show="!esValido && tienePorcentajesInvalidos">
                        Hay porcentajes inválidos. Solo se permiten valores entre 0 y 100.
                    </span>
                </p>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <a href="{{ route('grupos.show', ['grupo' => $grupo, 'tab' => 'config-evaluaciones']) }}"
                   class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    Cancelar
                </a>

                @if($puedeEditar)
                    <button type="submit"
                            :disabled="!esValido"
                            class="rounded-lg bg-slate-900 px-5 py-2 text-sm font-semibold text-white hover:bg-slate-800 disabled:opacity-40">
                        Guardar configuración
                    </button>
                @endif
            </div>
        </form>
    </div>

    <script>
        function configEvals(initial) {
            return {
                evaluaciones: Array.isArray(initial) && initial.length
                    ? initial.map(e => ({
                        nombre: e.nombre || '',
                        porcentaje: Number(e.porcentaje) || 0
                    }))
                    : [{
                        nombre: 'Evaluación 1',
                        porcentaje: 100
                    }],

                get suma() {
                    return this.evaluaciones.reduce(
                        (total, e) => {
                            const valor = Number(e.porcentaje);

                            return total + (
                                Number.isFinite(valor) ? valor : 0
                            );
                        },
                        0
                    );
                },

                get sumaFormateada() {
                    return this.suma.toFixed(2);
                },

                get tienePorcentajesInvalidos() {
                    return this.evaluaciones.some(e => {
                        const valor = Number(e.porcentaje);

                        return e.porcentaje === ''
                            || e.porcentaje === null
                            || !Number.isFinite(valor)
                            || valor < 0
                            || valor > 100;
                    });
                },

                get esValido() {
                    return Math.abs(this.suma - 100) < 0.01
                        && this.evaluaciones.length >= 1
                        && this.evaluaciones.length <= 50
                        && !this.tienePorcentajesInvalidos
                        && this.evaluaciones.every(
                            e => e.nombre && e.nombre.trim() !== ''
                        );
                },

                agregar() {
                    if (this.evaluaciones.length >= 50) {
                        return;
                    }

                    this.evaluaciones.push({
                        nombre: 'Evaluación ' + (this.evaluaciones.length + 1),
                        porcentaje: 0
                    });
                },

                quitar(i) {
                    if (this.evaluaciones.length > 1) {
                        this.evaluaciones.splice(i, 1);
                    }
                },

                repartirIgual() {
                    const n = this.evaluaciones.length;

                    if (!n) {
                        return;
                    }

                    const base = Math.floor((100 / n) * 100) / 100;

                    let acumulado = 0;

                    this.evaluaciones.forEach((e, i) => {
                        if (i === n - 1) {
                            e.porcentaje = Math.round(
                                (100 - acumulado) * 100
                            ) / 100;
                        } else {
                            e.porcentaje = base;
                            acumulado += base;
                        }
                    });
                }
            };
        }
    </script>
</x-app-layout>