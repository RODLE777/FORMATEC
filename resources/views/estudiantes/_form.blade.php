
@php
    $estudiante = $estudiante ?? null;
    $encargado  = $estudiante?->encargadoMenor;
@endphp

<div x-data="{
        fechaNacimiento: @js(old('fecha_nacimiento', $estudiante?->fecha_nacimiento?->format('Y-m-d') ?? '')),
        dui: @js(old('dui', $estudiante?->dui ?? '')),

        get esMenor() {
            if (!this.fechaNacimiento) return false;

            const hoy = new Date();
            const partes = this.fechaNacimiento.split('-');

            if (partes.length !== 3) return false;

            const anio = Number(partes[0]);
            const mes = Number(partes[1]);
            const dia = Number(partes[2]);

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

        formatearDui() {
            let numeros = String(this.dui || '')
                .replace(/\D/g, '')
                .slice(0, 9);

            if (numeros.length > 8) {
                this.dui = numeros.slice(0, 8)
                    + '-'
                    + numeros.slice(8);
            } else {
                this.dui = numeros;
            }
        }
    }"
    x-init="formatearDui()"
    class="space-y-6">

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-slate-700">
                Nombres *
            </label>

            <input
                type="text"
                name="nombres"
                value="{{ old('nombres', $estudiante->nombres ?? '') }}"
                required
                class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
            >

            @error('nombres')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700">
                Apellidos *
            </label>

            <input
                type="text"
                name="apellidos"
                value="{{ old('apellidos', $estudiante->apellidos ?? '') }}"
                required
                class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
            >

            @error('apellidos')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-slate-700">
                Sexo *
            </label>

            <select
                name="sexo"
                required
                class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
            >
                <option value="">— Seleccione —</option>

                <option
                    value="MASCULINO"
                    {{ old('sexo', $estudiante->sexo ?? '') === 'MASCULINO' ? 'selected' : '' }}
                >
                    Masculino
                </option>

                <option
                    value="FEMENINO"
                    {{ old('sexo', $estudiante->sexo ?? '') === 'FEMENINO' ? 'selected' : '' }}
                >
                    Femenino
                </option>
            </select>

            @error('sexo')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700">
                Fecha de nacimiento *
            </label>

            <input
                type="date"
                name="fecha_nacimiento"
                x-model="fechaNacimiento"
                required
                value="{{ old('fecha_nacimiento', $estudiante?->fecha_nacimiento?->format('Y-m-d') ?? '') }}"
                class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
            >

            @error('fecha_nacimiento')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-slate-700">
                DUI

                <span x-show="!esMenor">*</span>

                <span
                    x-show="esMenor"
                    x-cloak
                    class="text-xs text-slate-400"
                >
                    (opcional para menores)
                </span>
            </label>

            <input
                type="text"
                name="dui"
                id="dui"
                x-model="dui"
                x-on:input="formatearDui()"
                :required="!esMenor"
                placeholder="00000000-0"
                maxlength="10"
                inputmode="numeric"
                pattern="[0-9]{8}-[0-9]"
                title="Ingrese el DUI con el formato 00000000-0"
                autocomplete="off"
                class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
            >

            <p class="mt-1 text-xs text-slate-400">
                Formato: 00000000-0
            </p>

            @error('dui')
                <p class="mt-1 text-xs text-red-600">
                    {{ $message }}
                </p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700">
                Correo
            </label>

            <input
                type="email"
                name="correo"
                value="{{ old('correo', $estudiante->correo ?? '') }}"
                class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
            >

            @error('correo')
                <p class="mt-1 text-xs text-red-600">
                    {{ $message }}
                </p>
            @enderror
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-slate-700">
                Teléfono celular
            </label>

            <input
                type="text"
                name="telefono_celular"
                value="{{ old('telefono_celular', $estudiante->telefono_celular ?? '') }}"
                class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
            >
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700">
                Dirección
            </label>

            <input
                type="text"
                name="direccion"
                value="{{ old('direccion', $estudiante->direccion ?? '') }}"
                class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
            >
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-slate-700">
                Profesión / oficio
            </label>

            <input
                type="text"
                name="profesion_oficio"
                value="{{ old('profesion_oficio', $estudiante->profesion_oficio ?? '') }}"
                class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
            >
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700">
                Nivel de estudio
            </label>

            <input
                type="text"
                name="nivel_estudio"
                value="{{ old('nivel_estudio', $estudiante->nivel_estudio ?? '') }}"
                class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
            >
        </div>
    </div>

    {{-- Datos del encargado: solo visibles/obligatorios si es menor --}}
    <div
        x-show="esMenor"
        x-cloak
        class="rounded-xl border border-amber-200 bg-amber-50 p-4 space-y-3"
    >
        <p class="text-sm font-semibold text-amber-800">
            Datos del encargado (obligatorio para menores de edad)
        </p>

        <div>
            <label class="block text-sm font-medium text-slate-700">
                Nombre completo del encargado *
            </label>

            <input
                type="text"
                name="encargado_nombre_completo"
                value="{{ old('encargado_nombre_completo', $encargado->nombre_completo ?? '') }}"
                :required="esMenor"
                class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
            >

            @error('encargado_nombre_completo')
                <p class="mt-1 text-xs text-red-600">
                    {{ $message }}
                </p>
            @enderror
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700">
                    Parentesco
                </label>

                <input
                    type="text"
                    name="encargado_parentesco"
                    value="{{ old('encargado_parentesco', $encargado->parentesco ?? '') }}"
                    class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                >
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700">
                    Teléfono
                </label>

                <input
                    type="text"
                    name="encargado_telefono"
                    value="{{ old('encargado_telefono', $encargado->telefono ?? '') }}"
                    class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                >
            </div>
        </div>
    </div>

    <label class="flex items-center gap-2 text-sm text-slate-700">
        <input
            type="hidden"
            name="activo"
            value="0"
        >

        <input
            type="checkbox"
            name="activo"
            value="1"
            {{ old('activo', $estudiante->activo ?? true) ? 'checked' : '' }}
        >

        Estudiante activo
    </label>

    <button
        type="submit"
        class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800"
    >
        {{ $estudiante && $estudiante->exists
            ? 'Guardar cambios'
            : 'Registrar estudiante' }}
    </button>
</div>
