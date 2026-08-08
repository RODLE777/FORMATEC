@php $usuario = $usuario ?? null; @endphp

<div>
    <label class="block text-sm font-medium text-slate-700">Nombre completo</label>
    <input type="text" name="name" value="{{ old('name', $usuario->name ?? '') }}" required
           class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
    @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
</div>

<div>
    <label class="block text-sm font-medium text-slate-700">Correo</label>
    <input type="email" name="email" value="{{ old('email', $usuario->email ?? '') }}" required
           class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
    @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
</div>

<div class="grid grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium text-slate-700">
            Contrasena {{ $usuario ? '(dejar vacio para no cambiar)' : '' }}
        </label>
        <input type="password" name="password" {{ $usuario ? '' : 'required' }}
               class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
        @error('password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700">Confirmar contrasena</label>
        <input type="password" name="password_confirmation"
               class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
    </div>
</div>

<div>
    <label class="block text-sm font-medium text-slate-700">Rol</label>
    <select name="rol" required class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
        @foreach (['ROOT', 'ADMINISTRADOR', 'PROFESOR'] as $rol)
            <option value="{{ $rol }}" {{ old('rol', $usuario->rol ?? '') === $rol ? 'selected' : '' }}>{{ $rol }}</option>
        @endforeach
    </select>
</div>

<label class="flex items-center gap-2 text-sm text-slate-700">
    <input type="checkbox" name="activo" value="1" {{ old('activo', $usuario->activo ?? true) ? 'checked' : '' }}>
    Cuenta activa
</label>
