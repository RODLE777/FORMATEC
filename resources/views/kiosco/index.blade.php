
<x-kiosco-layout>
<style>
    [x-cloak] {
        display: none !important;
    }

    .ki {
        display: block;
        width: 100%;
        margin-top: 7px;
        padding: 11px 13px;
        border: 1.5px solid #cbd5e1 !important;
        border-radius: 11px;
        background: white !important;
        color: #0f172a;
        outline: none;
    }

    .ki:focus {
        border-color: #059669 !important;
        box-shadow: 0 0 0 3px #10b98122;
    }

    .kl {
        display: block;
        font-size: 13px;
        font-weight: 650;
        color: #334155;
    }

    .kb {
        padding: 12px 22px;
        border-radius: 11px;
        font-weight: 700;
    }

    .panel {
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 18px;
    }
</style>

<div
    x-data="kioscoApp()"
    class="max-w-4xl mx-auto bg-white border border-slate-200
           rounded-2xl shadow-lg p-6 sm:p-9"
>
    <div class="mb-7">
        <p class="text-xs font-bold tracking-widest text-emerald-700">
            FORMATEC · INSCRIPCIONES
        </p>

        <h1 class="text-3xl font-bold mt-2 text-slate-900">
            Inscríbete a nuestros cursos
        </h1>

        <p class="text-slate-500 mt-2 text-sm">
            Completa tus datos o recupera tu carnet si ya
            estudiaste con nosotros.
        </p>
    </div>

    @if($errors->any())
        <div class="mb-5 rounded-xl bg-red-50 border
                    border-red-200 p-4 text-red-800">
            <strong>Revisa estos datos:</strong>

            @foreach($errors->all() as $error)
                <p class="text-sm mt-1">{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <!-- OPCIONES INICIALES -->
    <div
        x-show="modo === ''"
        class="grid sm:grid-cols-2 gap-4"
    >
        <button
            type="button"
            @click="modo = 'existente'"
            class="panel text-left hover:border-emerald-500
                   hover:bg-emerald-50"
        >
            <span class="text-3xl">🎓</span>

            <h2 class="text-xl font-bold mt-3">
                Ya tengo carnet
            </h2>

            <p class="text-sm text-slate-500 mt-2">
                Inscríbete en otro curso conservando tu
                código e historial.
            </p>

            <p class="text-emerald-700 font-bold mt-4">
                Continuar →
            </p>
        </button>

        <button
            type="button"
            @click="modo = 'nuevo'"
            class="panel text-left hover:border-emerald-500
                   hover:bg-emerald-50"
        >
            <span class="text-3xl">📝</span>

            <h2 class="text-xl font-bold mt-3">
                Soy estudiante nuevo
            </h2>

            <p class="text-sm text-slate-500 mt-2">
                Regístrate por primera vez y elige el
                curso que deseas.
            </p>

            <p class="text-emerald-700 font-bold mt-4">
                Comenzar →
            </p>
        </button>
    </div>

    <!-- RECUPERAR FICHA EXISTENTE -->
    <section x-show="modo === 'existente'" x-cloak>
        <button
            type="button"
            @click="modo = ''"
            class="text-sm text-slate-500 mb-5"
        >
            ← Volver
        </button>

        <h2 class="text-xl font-bold">
            Recupera tu ficha
        </h2>

        <p class="text-slate-500 text-sm mt-2 mb-6">
            Un encargado verificará tu identidad antes
            de mostrar tus datos.
        </p>

        <form
            method="POST"
            action="{{ route('kiosco.buscar') }}"
            class="space-y-5"
        >
            @csrf

            <label class="kl">
                Buscar por

                <select
                    class="ki"
                    x-model="tipoBusqueda"
                    name="tipo_identificador"
                >
                    <option value="carnet">
                        Carnet FORMATEC
                    </option>

                    <option value="dui">
                        DUI
                    </option>
                </select>
            </label>

            <label class="kl">
                Número de
                <span
                    x-text="tipoBusqueda === 'dui'
                        ? 'DUI'
                        : 'carnet'"
                ></span>

                <input
                    x-show="tipoBusqueda === 'carnet'"
                    :disabled="tipoBusqueda !== 'carnet'"
                    name="identificador"
                    x-model="carnet"
                    class="ki uppercase"
                    placeholder="FTEC-000044"
                >

                <input
                    x-show="tipoBusqueda === 'dui'"
                    x-cloak
                    :disabled="tipoBusqueda !== 'dui'"
                    name="identificador"
                    x-model="duiBusqueda"
                    @input="duiBusqueda = formatoDui($event.target.value)"
                    inputmode="numeric"
                    maxlength="10"
                    class="ki"
                    placeholder="00000000-0"
                >
            </label>

            <button
                class="kb bg-emerald-700 text-white w-full"
                type="submit"
            >
                Continuar con verificación
            </button>
        </form>
    </section>

    <!-- REGISTRO NUEVO -->
    <section x-show="modo === 'nuevo'" x-cloak>
        <div class="flex items-center gap-4 mb-6">
            <button
                type="button"
                @click="paso === 1 ? modo = '' : paso--"
                class="text-sm text-slate-500"
            >
                ← Atrás
            </button>

            <div class="flex-1 flex gap-2">
                <template x-for="n in 3" :key="n">
                    <div
                        class="h-2 flex-1 rounded-full"
                        :class="paso >= n
                            ? 'bg-emerald-600'
                            : 'bg-slate-200'"
                    ></div>
                </template>
            </div>

            <span
                class="text-xs text-slate-500"
                x-text="paso + '/3'"
            ></span>
        </div>

        <form
            method="POST"
            action="{{ route('kiosco.registrar') }}"
            @submit="enviando = true"
        >
            @csrf

            <!-- PASO 1 -->
            <div x-show="paso === 1">
                <h2 class="text-xl font-bold mb-1">
                    Tus datos personales
                </h2>

                <p class="text-sm text-slate-500 mb-5">
                    Comprobaremos si ya tienes una ficha
                    antes de continuar.
                </p>

                <div class="grid sm:grid-cols-2 gap-4">
                    <label class="kl">
                        Nombres *

                        <input
                            class="ki"
                            name="nombres"
                            x-model="datos.nombres"
                            maxlength="150"
                        >
                    </label>

                    <label class="kl">
                        Apellidos *

                        <input
                            class="ki"
                            name="apellidos"
                            x-model="datos.apellidos"
                            maxlength="150"
                        >
                    </label>

                    <label class="kl">
                        Sexo *

                        <select
                            class="ki"
                            name="sexo"
                            x-model="datos.sexo"
                        >
                            <option value="">
                                Selecciona
                            </option>

                            <option value="MASCULINO">
                                Masculino
                            </option>

                            <option value="FEMENINO">
                                Femenino
                            </option>
                        </select>
                    </label>

                    <label class="kl">
                        Fecha de nacimiento *

                        <input
                            type="date"
                            max="{{ now()->subDay()->toDateString() }}"
                            class="ki"
                            name="fecha_nacimiento"
                            x-model="datos.fecha_nacimiento"
                        >
                    </label>

                    <label class="kl">
                        <span
                            x-text="esMenor
                                ? 'DUI (opcional)'
                                : 'DUI *'"
                        ></span>

                        <input
                            class="ki"
                            name="dui"
                            x-model="datos.dui"
                            @input="
                                datos.dui = formatoDui($event.target.value);
                                aviso = null
                            "
                            maxlength="10"
                            inputmode="numeric"
                            placeholder="00000000-0"
                        >
                    </label>

                    <label class="kl">
                        Teléfono celular *

                        <input
                            class="ki"
                            name="telefono_celular"
                            x-model="datos.telefono_celular"
                            placeholder="7000-0000"
                        >
                    </label>

                    <label class="kl">
                        Correo electrónico

                        <input
                            class="ki"
                            type="email"
                            name="correo"
                            x-model="datos.correo"
                        >
                    </label>

                    <label class="kl">
                        Dirección

                        <input
                            class="ki"
                            name="direccion"
                            x-model="datos.direccion"
                        >
                    </label>

                    <label class="kl">
                        Profesión u oficio

                        <input
                            class="ki"
                            name="profesion_oficio"
                            x-model="datos.profesion_oficio"
                        >
                    </label>

                    <label class="kl">
                        Nivel de estudio

                        <input
                            class="ki"
                            name="nivel_estudio"
                            x-model="datos.nivel_estudio"
                        >
                    </label>
                </div>

                <!-- ENCARGADO DE MENOR -->
                <div
                    x-show="esMenor"
                    x-cloak
                    class="mt-5 panel bg-amber-50"
                >
                    <h3 class="font-bold text-amber-900 mb-4">
                        Datos del encargado
                    </h3>

                    <div class="grid sm:grid-cols-2 gap-4">
                        <label class="kl sm:col-span-2">
                            Nombre completo *

                            <input
                                class="ki"
                                name="encargado_nombre_completo"
                                x-model="datos.encargado_nombre_completo"
                            >
                        </label>

                        <label class="kl">
                            Parentesco

                            <input
                                class="ki"
                                name="encargado_parentesco"
                                x-model="datos.encargado_parentesco"
                            >
                        </label>

                        <label class="kl">
                            Teléfono

                            <input
                                class="ki"
                                name="encargado_telefono"
                                x-model="datos.encargado_telefono"
                            >
                        </label>
                    </div>
                </div>

                <!-- AVISO DE IDENTIDAD -->
                <div
                    x-show="aviso"
                    x-cloak
                    class="mt-5 panel bg-blue-50 border-blue-200"
                    role="status"
                    aria-live="polite"
                >
                    <h3
                        class="font-bold text-blue-900"
                        x-text="aviso?.titulo"
                    ></h3>

                    <p
                        class="text-sm text-blue-800 mt-2"
                        x-text="aviso?.mensaje"
                    ></p>

                    <div
                        x-show="aviso?.tipo === 'dui_registrado'"
                        class="mt-4 flex flex-wrap gap-2"
                    >
                        <button
                            type="button"
                            @click="
                                modo = 'existente';
                                tipoBusqueda = 'dui';
                                duiBusqueda = datos.dui;
                                aviso = null
                            "
                            class="kb bg-emerald-700 text-white"
                        >
                            Recuperar mi carnet
                        </button>

                        <button
                            type="button"
                            @click="aviso = null"
                            class="kb border border-slate-300"
                        >
                            Corregir mis datos
                        </button>
                    </div>

                    <div
                        x-show="aviso?.tipo === 'coincidencia'"
                        class="mt-3 text-xs text-blue-800"
                    >
                        Podrás continuar y el personal revisará
                        la coincidencia antes de crear un
                        carnet nuevo.
                    </div>
                </div>
            </div>

            <!-- PASO 2 -->
            <div x-show="paso === 2" x-cloak>
                <h2 class="text-xl font-bold mb-5">
                    Selecciona tu curso
                </h2>

                <div class="grid gap-3">
                    @forelse($grupos as $grupo)
                        <label
                            class="panel flex gap-3 cursor-pointer
                                   hover:border-emerald-500"
                        >
                            <input
                                type="radio"
                                class="mt-1"
                                name="grupo_id"
                                value="{{ $grupo->id }}"
                                x-model="datos.grupo_id"
                            >

                            <div>
                                <p class="font-bold">
                                    {{ $grupo->curso->nombre ?? 'Curso' }}
                                </p>

                                <p class="text-sm text-slate-500">
                                    Grupo {{ $grupo->codigo_grupo }}
                                    ·
                                    {{ $grupo->horario ?? 'Horario por confirmar' }}
                                </p>

                                <p class="text-xs text-emerald-700 mt-1">
                                    Cupos:
                                    {{ $grupo->cuposDisponibles() ?? 'Sin límite' }}
                                </p>
                            </div>
                        </label>
                    @empty
                        <p class="text-slate-500">
                            Por el momento no hay grupos disponibles.
                        </p>
                    @endforelse
                </div>
            </div>

            <!-- PASO 3 -->
            <div x-show="paso === 3" x-cloak>
                <h2 class="text-xl font-bold mb-5">
                    Confirma tu inscripción
                </h2>

                <div class="panel bg-slate-50 space-y-3 text-sm">
                    <p>
                        <strong>Nombre:</strong>
                        <span
                            x-text="datos.nombres + ' ' + datos.apellidos"
                        ></span>
                    </p>

                    <p>
                        <strong>Teléfono:</strong>
                        <span x-text="datos.telefono_celular"></span>
                    </p>

                    <p>
                        <strong>DUI:</strong>
                        <span
                            x-text="datos.dui || 'No proporcionado'"
                        ></span>
                    </p>

                    <div class="border-t border-slate-200 pt-4">
                        <p class="text-xs uppercase text-slate-500">
                            Curso seleccionado
                        </p>

                        <p
                            class="text-lg font-bold text-emerald-700"
                            x-text="cursoSeleccionado?.nombre || 'Sin seleccionar'"
                        ></p>

                        <p
                            class="text-slate-600 mt-1"
                            x-text="'Grupo: ' + (cursoSeleccionado?.codigo || '')"
                        ></p>

                        <p
                            class="text-slate-600"
                            x-text="'Horario: ' + (cursoSeleccionado?.horario || 'Por confirmar')"
                        ></p>
                    </div>
                </div>

                <p class="text-sm text-slate-500 mt-4">
                    Si se detecta una coincidencia, un encargado
                    verificará tu identidad antes de completar
                    el registro.
                </p>
            </div>

            <!-- BOTONES -->
            <div class="flex justify-end mt-7">
                <button
                    x-show="paso < 3"
                    type="button"
                    :disabled="verificando"
                    @click="siguiente()"
                    class="kb bg-slate-900 text-white
                           disabled:opacity-50"
                >
                    <span
                        x-text="verificando
                            ? 'Comprobando...'
                            : 'Siguiente →'"
                    ></span>
                </button>

                <button
                    x-show="paso === 3"
                    x-cloak
                    type="submit"
                    :disabled="enviando"
                    class="kb bg-emerald-700 text-white
                           disabled:opacity-50"
                >
                    Confirmar inscripción
                </button>
            </div>
        </form>
    </section>
</div>

<script>
function kioscoApp() {
    return {
        modo: @js($errors->any() ? 'nuevo' : ''),
        paso: 1,
        enviando: false,
        verificando: false,
        aviso: null,

        tipoBusqueda: 'carnet',
        carnet: '',
        duiBusqueda: '',

        cursos: @js($grupos->map(fn ($g) => [
            'id' => (string) $g->id,
            'nombre' => $g->curso->nombre ?? 'Curso',
            'codigo' => $g->codigo_grupo,
            'horario' => $g->horario ?? 'Por confirmar'
        ])->values()),

        datos: {
            nombres: @js(old('nombres', '')),
            apellidos: @js(old('apellidos', '')),
            sexo: @js(old('sexo', '')),
            fecha_nacimiento: @js(old('fecha_nacimiento', '')),
            dui: @js(old('dui', '')),
            telefono_celular: @js(old('telefono_celular', '')),
            correo: @js(old('correo', '')),
            direccion: @js(old('direccion', '')),
            profesion_oficio: @js(old('profesion_oficio', '')),
            nivel_estudio: @js(old('nivel_estudio', '')),
            encargado_nombre_completo: @js(old('encargado_nombre_completo', '')),
            encargado_parentesco: @js(old('encargado_parentesco', '')),
            encargado_telefono: @js(old('encargado_telefono', '')),
            grupo_id: @js((string) old('grupo_id', ''))
        },

        get cursoSeleccionado() {
            return this.cursos.find(
                c => c.id === String(this.datos.grupo_id)
            ) || null;
        },

        formatoDui(valor) {
            const numeros = String(valor)
                .replace(/\D/g, '')
                .slice(0, 9);

            return numeros.length > 8
                ? numeros.slice(0, 8) + '-' + numeros.slice(8)
                : numeros;
        },

        get esMenor() {
            if (!this.datos.fecha_nacimiento) {
                return false;
            }

            const [anio, mes, dia] = this.datos.fecha_nacimiento
                .split('-')
                .map(Number);

            const hoy = new Date();

            let edad = hoy.getFullYear() - anio;

            if (
                hoy.getMonth() + 1 < mes ||
                (
                    hoy.getMonth() + 1 === mes &&
                    hoy.getDate() < dia
                )
            ) {
                edad--;
            }

            return edad < 18;
        },

        async siguiente() {
            if (this.paso === 1) {
                const obligatorios = [
                    'nombres',
                    'apellidos',
                    'sexo',
                    'fecha_nacimiento',
                    'telefono_celular'
                ];

                for (const campo of obligatorios) {
                    if (!String(this.datos[campo]).trim()) {
                        alert('Completa todos los campos obligatorios.');
                        return;
                    }
                }

                if (
                    !this.esMenor &&
                    !/^\d{8}-\d$/.test(this.datos.dui)
                ) {
                    alert(
                        'Ingresa un DUI válido con formato 00000000-0.'
                    );
                    return;
                }

                if (
                    this.datos.dui &&
                    !/^\d{8}-\d$/.test(this.datos.dui)
                ) {
                    alert('Revisa el formato del DUI.');
                    return;
                }

                if (
                    this.esMenor &&
                    !this.datos.encargado_nombre_completo.trim()
                ) {
                    alert('Ingresa el nombre del encargado.');
                    return;
                }

                this.verificando = true;
                this.aviso = null;

                try {
                    const respuesta = await fetch(
                        @js(route('kiosco.verificar')),
                        {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN':
                                    document.querySelector(
                                        'input[name="_token"]'
                                    ).value
                            },
                            credentials: 'same-origin',
                            body: JSON.stringify({
                                nombres: this.datos.nombres,
                                apellidos: this.datos.apellidos,
                                sexo: this.datos.sexo,
                                fecha_nacimiento:
                                    this.datos.fecha_nacimiento,
                                dui: this.datos.dui
                            })
                        }
                    );

                    const json = await respuesta.json();

                    if (!respuesta.ok) {
                        const mensaje = Object.values(
                            json.errors || {}
                        ).flat().join(' ')
                            || json.message
                            || 'No se pudo comprobar la información.';

                        this.aviso = {
                            tipo: 'error',
                            titulo: 'Revisa tus datos',
                            mensaje
                        };

                        return;
                    }

                    if (json.estado === 'dui_registrado') {
                        this.aviso = {
                            tipo: 'dui_registrado',
                            titulo: '¡Ya tienes un carnet FORMATEC!',
                            mensaje:
                                'Este DUI ya está registrado. ' +
                                'Puedes recuperar tu ficha y conservar ' +
                                'tu historial sin crear otra cuenta.'
                        };

                        return;
                    }

                    if (json.estado === 'coincidencia') {
                        this.aviso = {
                            tipo: 'coincidencia',
                            titulo: 'Encontramos una posible ficha anterior',
                            mensaje:
                                'Puedes continuar con tu inscripción. ' +
                                'Antes de crear un carnet, un encargado ' +
                                'comprobará si ya tienes una ficha.'
                        };
                    }

                    this.paso = 2;

                } catch (error) {
                    this.aviso = {
                        tipo: 'error',
                        titulo: 'No pudimos comprobar tus datos',
                        mensaje:
                            'Verifica tu conexión e inténtalo de nuevo.'
                    };

                } finally {
                    this.verificando = false;
                }

                return;
            }

            if (this.paso === 2 && !this.datos.grupo_id) {
                alert('Selecciona un curso.');
                return;
            }

            if (this.paso < 3) {
                this.paso++;
            }
        }
    };
}
</script>
</x-kiosco-layout>
