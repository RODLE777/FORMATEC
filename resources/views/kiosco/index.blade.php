<x-kiosco-layout>
    <div x-data="kioscoWizard()" class="bg-white rounded-2xl shadow-lg border border-slate-200 p-6 sm:p-8">

        <h1 class="text-2xl font-bold text-slate-900 mb-1">Regístrate en un curso</h1>
        <p class="text-sm text-slate-500 mb-6">Completa los pasos para inscribirte.</p>

        {{-- Progreso --}}
        <div class="flex items-center gap-2 mb-8">
            <template x-for="n in totalSteps" :key="n">
                <div class="flex-1">
                    <div class="h-2 rounded-full transition"
                         :class="step >= n ? 'bg-emerald-500' : 'bg-slate-200'"></div>
                    <p class="mt-1 text-xs text-center"
                       :class="step === n ? 'text-emerald-600 font-semibold' : 'text-slate-400'"
                       x-text="tituloPaso(n)"></p>
                </div>
            </template>
        </div>

        <form method="POST" action="{{ route('kiosco.registrar') }}">
            @csrf

            {{-- ============ PASO 1: DATOS PERSONALES ============ --}}
            <div x-show="step === 1" x-cloak class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Nombres *</label>
                        <input type="text" name="nombres" x-model="data.nombres" required
                               class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Apellidos *</label>
                        <input type="text" name="apellidos" x-model="data.apellidos" required
                               class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Sexo *</label>
                        <select name="sexo" x-model="data.sexo" required
                                class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                            <option value="">— Seleccione —</option>
                            <option value="MASCULINO">Masculino</option>
                            <option value="FEMENINO">Femenino</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Fecha de nacimiento *</label>
                        <input type="date" name="fecha_nacimiento" x-model="data.fecha_nacimiento" required
                               class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700">
                            DUI <span x-show="!esMenor">*</span>
                            <span x-show="esMenor" class="text-xs text-slate-400">(opcional)</span>
                        </label>
                        <input type="text" name="dui" x-model="data.dui" :required="!esMenor"
                               class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Correo (opcional)</label>
                        <input type="email" name="correo" x-model="data.correo"
                               class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Teléfono celular (opcional)</label>
                        <input type="text" name="telefono_celular" x-model="data.telefono_celular"
                               class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Dirección (opcional)</label>
                        <input type="text" name="direccion" x-model="data.direccion"
                               class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Profesión / oficio (opcional)</label>
                        <input type="text" name="profesion_oficio" x-model="data.profesion_oficio"
                               class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Nivel de estudio (opcional)</label>
                        <input type="text" name="nivel_estudio" x-model="data.nivel_estudio"
                               class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    </div>
                </div>

                <div x-show="esMenor" x-cloak class="rounded-xl border border-amber-200 bg-amber-50 p-4 space-y-3">
                    <p class="text-sm font-semibold text-amber-800">Datos del encargado (obligatorio para menores)</p>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Nombre completo *</label>
                        <input type="text" name="encargado_nombre_completo" x-model="data.encargado_nombre_completo"
                               :required="esMenor"
                               class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Parentesco</label>
                            <input type="text" name="encargado_parentesco" x-model="data.encargado_parentesco"
                                   class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Teléfono</label>
                            <input type="text" name="encargado_telefono" x-model="data.encargado_telefono"
                                   class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============ PASO 2: CURSO ============ --}}
            <div x-show="step === 2" x-cloak class="space-y-3">
                <p class="text-sm text-slate-600">Selecciona el curso al que deseas inscribirte:</p>

                @forelse ($grupos as $grupo)
                    <label class="flex items-start gap-3 rounded-xl border border-slate-200 p-4 cursor-pointer hover:border-emerald-400 transition">
                        <input type="radio" name="grupo_id" value="{{ $grupo->id }}"
                               x-model="data.grupo_id" required
                               class="mt-1 text-emerald-600 focus:ring-emerald-500">
                        <div class="flex-1">
                            <p class="font-semibold text-slate-800">
                                {{ $grupo->curso->nombre ?? 'Curso #'.$grupo->id }}
                                @if ($grupo->codigo_grupo)
                                    <span class="text-xs text-slate-500">· {{ $grupo->codigo_grupo }}</span>
                                @endif
                            </p>
                            <p class="text-xs text-slate-500">
                                @if ($grupo->horario) Horario: {{ $grupo->horario }} @endif
                                @if ($grupo->lugar) · {{ $grupo->lugar }} @endif
                            </p>
                            @php $cupos = $grupo->cuposDisponibles(); @endphp
                            <p class="text-xs mt-1 {{ is_null($cupos) ? 'text-emerald-600' : ($cupos > 5 ? 'text-emerald-600' : 'text-amber-600') }}">
                                @if (is_null($cupos))
                                    Cupos ilimitados
                                @else
                                    {{ $cupos }} cupo(s) disponible(s)
                                @endif
                            </p>
                        </div>
                    </label>
                @empty
                    <p class="text-sm text-red-600">No hay cursos disponibles en este momento.</p>
                @endforelse
            </div>

            {{-- ============ PASO 3: CONFIRMACIÓN ============ --}}
            <div x-show="step === 3" x-cloak class="space-y-4">
                <p class="text-sm text-slate-600">Revisa tus datos antes de enviar:</p>

                <div class="rounded-xl bg-slate-50 border border-slate-200 p-4 text-sm space-y-2">
                    <p><span class="font-semibold">Nombre:</span> <span x-text="data.nombres + ' ' + data.apellidos"></span></p>
                    <p><span class="font-semibold">Sexo:</span> <span x-text="data.sexo"></span></p>
                    <p><span class="font-semibold">Fecha nac.:</span> <span x-text="data.fecha_nacimiento"></span></p>
                    <p><span class="font-semibold">DUI:</span> <span x-text="data.dui || '—'"></span></p>
                    <p><span class="font-semibold">Correo:</span> <span x-text="data.correo || '—'"></span></p>
                    <p><span class="font-semibold">Teléfono:</span> <span x-text="data.telefono_celular || '—'"></span></p>
                    <template x-if="esMenor">
                        <div class="pt-2 border-t border-slate-200">
                            <p class="font-semibold text-amber-800">Datos del encargado</p>
                            <p><span class="font-semibold">Nombre:</span> <span x-text="data.encargado_nombre_completo"></span></p>
                            <p><span class="font-semibold">Teléfono:</span> <span x-text="data.encargado_telefono || '—'"></span></p>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Botones --}}
            <div class="mt-8 flex justify-between">
                <button type="button" x-show="step > 1" @click="prev()"
                        class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    ← Atrás
                </button>
                <div class="flex-1"></div>
                <button type="button" x-show="step < totalSteps" @click="next()"
                        class="rounded-lg bg-slate-900 px-5 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                    Siguiente →
                </button>
                <button type="submit" x-show="step === totalSteps"
                        class="rounded-lg bg-emerald-600 px-5 py-2 text-sm font-semibold text-white hover:bg-emerald-700">
                    Inscribirme
                </button>
            </div>
        </form>
    </div>

    <script>
        function kioscoWizard() {
            return {
                step: 1,
                totalSteps: 3,
                data: {
                    nombres: '', apellidos: '', sexo: '', fecha_nacimiento: '',
                    dui: '', correo: '', telefono_celular: '', direccion: '',
                    profesion_oficio: '', nivel_estudio: '',
                    encargado_nombre_completo: '', encargado_parentesco: '', encargado_telefono: '',
                    grupo_id: '',
                },
                get esMenor() {
                    if (!this.data.fecha_nacimiento) return false;
                    const hoy = new Date();
                    const nac = new Date(this.data.fecha_nacimiento);
                    let edad = hoy.getFullYear() - nac.getFullYear();
                    const m = hoy.getMonth() - nac.getMonth();
                    if (m < 0 || (m === 0 && hoy.getDate() < nac.getDate())) edad--;
                    return edad < 18;
                },
                tituloPaso(n) {
                    return ['Datos', 'Curso', 'Confirmar'][n - 1] || '';
                },
                next() {
                    if (this.step === 1) {
                        if (!this.data.nombres || !this.data.apellidos || !this.data.sexo || !this.data.fecha_nacimiento) {
                            alert('Completa los campos obligatorios.'); return;
                        }
                        if (!this.esMenor && !this.data.dui) {
                            alert('El DUI es obligatorio.'); return;
                        }
                        if (this.esMenor && !this.data.encargado_nombre_completo) {
                            alert('Los datos del encargado son obligatorios.'); return;
                        }
                    }
                    if (this.step === 2 && !this.data.grupo_id) {
                        alert('Selecciona un curso.'); return;
                    }
                    if (this.step < this.totalSteps) this.step++;
                },
                prev() { if (this.step > 1) this.step--; }
            }
        }
    </script>
</x-kiosco-layout>