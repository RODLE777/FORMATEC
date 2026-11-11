
@php
    $profesor = $profesor ?? null;
@endphp

<div
    x-data="{
        dui: @js(old('dui', $profesor->dui ?? '')),

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
    class="space-y-4"
>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-slate-700">
                Nombres
            </label>

            <input
                type="text"
                name="nombres"
                value="{{ old('nombres', $profesor->nombres ?? '') }}"
                required
                class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
            >

            @error('nombres')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700">
                Apellidos
            </label>

            <input
                type="text"
                name="apellidos"
                value="{{ old('apellidos', $profesor->apellidos ?? '') }}"
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
                DUI
            </label>

            <input
                type="text"
                name="dui"
                id="dui"
                x-model="dui"
                x-on:input="formatearDui()"
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
                Teléfono
            </label>

            <input
                type="text"
                name="telefono"
                value="{{ old('telefono', $profesor->telefono ?? '') }}"
                class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
            >

            @error('telefono')
                <p class="mt-1 text-xs text-red-600">
                    {{ $message }}
                </p>
            @enderror
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700">
            Correo
        </label>

        <input
            type="email"
            name="correo"
            value="{{ old('correo', $profesor->correo ?? '') }}"
            class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
        >

        @error('correo')
            <p class="mt-1 text-xs text-red-600">
                {{ $message }}
            </p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700">
            Especialidad
        </label>

        <input
            type="text"
            name="especialidad"
            value="{{ old('especialidad', $profesor->especialidad ?? '') }}"
            class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
        >

        @error('especialidad')
            <p class="mt-1 text-xs text-red-600">
                {{ $message }}
            </p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700">
            Cuenta de acceso al sistema (opcional)
        </label>

        <select
            name="user_id"
            class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
        >
            <option value="">
                — Sin cuenta vinculada —
            </option>

            @foreach ($usuariosDisponibles as $u)
                <option
                    value="{{ $u->id }}"
                    {{ old('user_id', $profesor->user_id ?? '') == $u->id ? 'selected' : '' }}
                >
                    {{ $u->name }} ({{ $u->email }})
                </option>
            @endforeach
        </select>

        <p class="mt-1 text-xs text-slate-400">
            Solo aparecen usuarios con rol PROFESOR aún no vinculados
            a otro profesor.
        </p>

        @error('user_id')
            <p class="mt-1 text-xs text-red-600">
                {{ $message }}
            </p>
        @enderror
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
            {{ old('activo', $profesor->activo ?? true) ? 'checked' : '' }}
        >

        Profesor activo
    </label>

</div>
