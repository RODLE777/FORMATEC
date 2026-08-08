@php $profesor = $profesor ?? null; @endphp

<div class="grid grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium text-slate-700">Nombres</label>
        <input type="text" name="nombres" value="{{ old('nombres', $profesor->nombres ?? '') }}" required
               class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
        @error('nombres') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700">Apellidos</label>
        <input type="text" name="apellidos" value="{{ old('apellidos', $profesor->apellidos ?? '') }}" required
               class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
        @error('apellidos') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
    </div>
</div>

<div class="grid grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium text-slate-700">DUI</label>
        <input type="text" name="dui" value="{{ old('dui', $profesor->dui ?? '') }}"
               class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700">Telefono</label>
        <input type="text" name="telefono" value="{{ old('telefono', $profesor->telefono ?? '') }}"
               class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
    </div>
</div>

<div>
    <label class="block text-sm font-medium text-slate-700">Correo</label>
    <input type="email" name="correo" value="{{ old('correo', $profesor->correo ?? '') }}"
           class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
</div>

<div>
    <label class="block text-sm font-medium text-slate-700">Especialidad</label>
    <input type="text" name="especialidad" value="{{ old('especialidad', $profesor->especialidad ?? '') }}"
           class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
</div>

<div>
    <label class="block text-sm font-medium text-slate-700">Cuenta de acceso al sistema (opcional)</label>
    <select name="user_id" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
        <option value="">— Sin cuenta vinculada —</option>
        @foreach ($usuariosDisponibles as $u)
            <option value="{{ $u->id }}" {{ old('user_id', $profesor->user_id ?? '') == $u->id ? 'selected' : '' }}>
                {{ $u->name }} ({{ $u->email }})
            </option>
        @endforeach
    </select>
    <p class="mt-1 text-xs text-slate-400">Solo aparecen usuarios con rol PROFESOR aun no vinculados a otro profesor.</p>
</div>

<label class="flex items-center gap-2 text-sm text-slate-700">
    <input type="checkbox" name="activo" value="1" {{ old('activo', $profesor->activo ?? true) ? 'checked' : '' }}>
    Profesor activo
</label>
