
<x-kiosco-layout>
<style>
    [x-cloak] {
        display: none !important;
    }

    .ri {
        display: block;
        width: 100%;
        padding: 11px 13px;
        margin-top: 7px;
        border: 1.5px solid #cbd5e1 !important;
        border-radius: 11px;
        background: white !important;
    }

    .ri:focus {
        outline: none;
        border-color: #059669 !important;
    }

    .rc {
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 17px;
    }
</style>

<div class="max-w-3xl mx-auto bg-white border
            border-slate-200 rounded-2xl shadow-lg p-6 sm:p-9">

    <a
        href="{{ route('kiosco.index') }}"
        class="text-sm text-slate-500"
    >
        ← Cancelar y volver al inicio
    </a>

    <p class="text-xs font-bold tracking-widest
              text-emerald-700 mt-6">
        FORMATEC · VERIFICACIÓN
    </p>

    <h1 class="text-2xl font-bold mt-2">
        Te ayudamos con tu inscripción
    </h1>

    <p class="text-sm text-slate-500 mt-2 mb-6">
        Un encargado comprobará tu identidad para
        proteger tu información y evitar carnets duplicados.
    </p>

    @if($errors->any())
        <div class="mb-5 rc bg-red-50 border-red-200 text-red-800">
            @foreach($errors->all() as $error)
                <p class="text-sm">{{ $error }}</p>
            @endforeach
        </div>
    @endif

    @if(!$empleado)

        <!-- VERIFICACIÓN DEL PERSONAL -->
        <div class="rc bg-blue-50 border-blue-200 mb-6">
            <h2 class="font-bold text-blue-900">
                Un momento, necesitamos ayuda del personal
            </h2>

            <p class="text-sm text-blue-800 mt-2">
                Pide a un encargado de FORMATEC que confirme
                tu identidad. Tus datos no se mostrarán
                antes de la autorización.
            </p>
        </div>

        <form
            method="POST"
            action="{{ route('kiosco.autenticar') }}"
            class="space-y-4"
        >
            @csrf

            <label class="block text-sm font-semibold">
                Correo del encargado

                <input
                    type="email"
                    name="email"
                    autocomplete="off"
                    class="ri"
                    required
                >
            </label>

            <label class="block text-sm font-semibold">
                Contraseña

                <input
                    type="password"
                    name="password"
                    autocomplete="off"
                    class="ri"
                    required
                >
            </label>

            <button
                type="submit"
                class="w-full bg-emerald-700 text-white
                       font-bold py-3 rounded-xl"
            >
                Verificar personal
            </button>
        </form>

    @else

        <!-- PERSONAL AUTORIZADO -->
        <div class="rc bg-emerald-50 border-emerald-200 mb-6">
            <strong class="text-emerald-900">
                Personal verificado
            </strong>

            <p class="text-sm text-emerald-800">
                {{ $empleado->name }} · {{ $empleado->rol }}
            </p>

            <p class="text-xs text-emerald-700 mt-1">
                Esta autorización dura cinco minutos.
            </p>
        </div>

        @if($pendiente['tipo'] === 'existente')

            <!-- RECUPERACIÓN DE FICHA -->
            @if($estudiante)

                <div class="rc mb-5">
                    <h2 class="font-bold mb-3">
                        Ficha encontrada
                    </h2>

                    <p>
                        <strong>Carnet:</strong>
                        {{ $estudiante->codigo_formatec }}
                    </p>

                    <p>
                        <strong>Estudiante:</strong>
                        {{ $estudiante->nombres }}
                        {{ $estudiante->apellidos }}
                    </p>

                    <p>
                        <strong>Fecha de nacimiento:</strong>
                        {{
                            \Illuminate\Support\Carbon::parse(
                                $estudiante->fecha_nacimiento
                            )->format('d/m/Y')
                        }}
                    </p>
                </div>

                <form
                    method="POST"
                    action="{{ route('kiosco.resolver') }}"
                    class="space-y-4"
                >
                    @csrf

                    <h3 class="font-bold">
                        ¿A qué curso deseas inscribirte?
                    </h3>

                    <select
                        name="grupo_id"
                        class="ri"
                        required
                    >
                        <option value="">
                            Selecciona un curso
                        </option>

                        @foreach($grupos as $g)
                            <option value="{{ $g->id }}">
                                {{ $g->curso->nombre ?? 'Curso' }}
                                —
                                {{ $g->codigo_grupo }}
                            </option>
                        @endforeach
                    </select>

                    <label class="block text-sm font-semibold">
                        Cómo se comprobó la identidad

                        <textarea
                            class="ri"
                            name="motivo"
                            required
                            minlength="10"
                            maxlength="500"
                            rows="3"
                            placeholder="Ej.: Se verificó la identidad con documento presentado."
                        ></textarea>
                    </label>

                    <button
                        type="submit"
                        class="w-full bg-emerald-700 text-white
                               font-bold py-3 rounded-xl"
                    >
                        Confirmar inscripción con este carnet
                    </button>
                </form>

            @else

                <div class="rc bg-amber-50 border-amber-200">
                    <strong>
                        No encontramos ese carnet o DUI.
                    </strong>

                    <p class="text-sm mt-2">
                        Revisa el número ingresado. Si nunca
                        has estudiado en FORMATEC, puedes
                        iniciar un registro nuevo.
                    </p>
                </div>

            @endif

        @else

            <!-- POSIBLES COINCIDENCIAS -->
            <h2 class="text-lg font-bold mb-2">
                Posibles fichas anteriores
            </h2>

            <p class="text-sm text-slate-500 mb-5">
                Compara los datos con el documento presentado
                antes de decidir.
            </p>

            <form
                method="POST"
                action="{{ route('kiosco.resolver') }}"
                x-data="{
                    accion: 'existente',
                    estudiante: ''
                }"
                class="space-y-5"
            >
                @csrf

                <div class="space-y-3">
                    <label class="rc flex gap-3 cursor-pointer">
                        <input
                            type="radio"
                            name="accion"
                            value="existente"
                            x-model="accion"
                        >

                        <span>
                            <strong>
                                Sí, ya es estudiante FORMATEC
                            </strong>

                            <span class="block text-sm text-slate-500">
                                Conservar su carnet e historial.
                            </span>
                        </span>
                    </label>

                    <label class="rc flex gap-3 cursor-pointer">
                        <input
                            type="radio"
                            name="accion"
                            value="nuevo"
                            x-model="accion"
                        >

                        <span>
                            <strong>
                                No, es otra persona
                            </strong>

                            <span class="block text-sm text-slate-500">
                                Autorizar una ficha distinta tras
                                verificar la identidad.
                            </span>
                        </span>
                    </label>
                </div>

                <!-- SELECCIÓN DE FICHA -->
                <div
                    x-show="accion === 'existente'"
                    class="space-y-3"
                >
                    <h3 class="font-bold">
                        Selecciona la ficha comprobada
                    </h3>

                    @forelse($estudiantes as $e)
                        <label
                            class="rc flex gap-3 cursor-pointer
                                   hover:border-emerald-500"
                        >
                            <input
                                type="radio"
                                name="estudiante_id"
                                value="{{ $e->id }}"
                                x-model="estudiante"
                            >

                            <span>
                                <strong>
                                    {{ $e->nombres }}
                                    {{ $e->apellidos }}
                                </strong>

                                <span class="block text-sm text-slate-500">
                                    {{ $e->codigo_formatec }}
                                    ·
                                    {{
                                        \Illuminate\Support\Carbon::parse(
                                            $e->fecha_nacimiento
                                        )->format('d/m/Y')
                                    }}
                                </span>
                            </span>
                        </label>
                    @empty
                        <p class="text-sm text-amber-700">
                            No se encontró una ficha seleccionable.
                            Comprueba los datos antes de continuar.
                        </p>
                    @endforelse
                </div>

                <!-- AUTORIZAR PERSONA DIFERENTE -->
                <div
                    x-show="accion === 'nuevo'"
                    x-cloak
                    class="rc bg-amber-50 border-amber-200 text-sm"
                >
                    Se creará un carnet nuevo. Si el DUI ya
                    pertenece a una ficha, no se permitirá
                    duplicarlo.
                </div>

                <label class="block text-sm font-semibold">
                    Motivo de la decisión

                    <textarea
                        class="ri"
                        name="motivo"
                        minlength="10"
                        maxlength="500"
                        required
                        rows="3"
                        placeholder="Describe qué documentos o datos se verificaron."
                    ></textarea>
                </label>

                <button
                    type="submit"
                    class="w-full bg-emerald-700 text-white
                           font-bold py-3 rounded-xl"
                >
                    Confirmar decisión e inscripción
                </button>
            </form>

        @endif

    @endif
</div>
</x-kiosco-layout>
