@php $grupo = $grupo ?? null; @endphp

<div class="grid grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium text-slate-700">Curso</label>
        <select name="curso_id" required class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            <option value="">— Selecciona —</option>
            @foreach ($cursos as $c)
                <option value="{{ $c->id }}" {{ old('curso_id', $grupo->curso_id ?? '') == $c->id ? 'selected' : '' }}>{{ $c->nombre }}</option>
            @endforeach
        </select>
        @error('curso_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700">Profesor</label>
        <select name="profesor_id" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            <option value="">Sin asignar</option>
            @foreach ($profesores as $p)
                <option value="{{ $p->id }}" {{ old('profesor_id', $grupo->profesor_id ?? '') == $p->id ? 'selected' : '' }}>{{ $p->nombre_completo }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="grid grid-cols-3 gap-4">
    <div>
        <label class="block text-sm font-medium text-slate-700">Código de grupo</label>
        <input type="text" name="codigo_grupo" value="{{ old('codigo_grupo', $grupo->codigo_grupo ?? '') }}" required
               placeholder="ej. EXCEL-G16" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
        @error('codigo_grupo') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700">Año</label>
        <input type="number" name="anio" value="{{ old('anio', $grupo->anio ?? date('Y')) }}" required
               class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700">Mes de inicio (1-12)</label>
        <input type="number" min="1" max="12" name="mes" value="{{ old('mes', $grupo->mes ?? '') }}" required
               class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
    </div>
</div>

<div class="grid grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium text-slate-700">Fecha de inicio</label>
        
        <input type="date" name="fecha_inicio" value="{{ old('fecha_inicio', $grupo->fecha_inicio?->format('Y-m-d') ?? '') }}"
               class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700">Fecha de fin</label>
        <input type="date" name="fecha_fin" value="{{ old('fecha_fin', $grupo->fecha_fin?->format('Y-m-d') ?? '') }}"
               class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
    </div>
</div>

<div class="grid grid-cols-3 gap-4">
    <div>
        <label class="block text-sm font-medium text-slate-700">Duración (horas)</label>
        <input type="number" name="duracion_horas" value="{{ old('duracion_horas', $grupo->duracion_horas ?? '') }}"
               class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700">Horario</label>
        <input type="text" name="horario" value="{{ old('horario', $grupo->horario ?? '') }}"
               class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700">Lugar</label>
        <input type="text" name="lugar" value="{{ old('lugar', $grupo->lugar ?? '') }}"
               class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
    </div>
</div>

<div class="grid grid-cols-3 gap-4">
    <div>
        <label class="block text-sm font-medium text-slate-700">Número de evaluaciones</label>
        <input type="number" min="1" max="20" name="numero_evaluaciones"
               value="{{ old('numero_evaluaciones', $grupo->numero_evaluaciones ?? 3) }}" required
               class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
        <p class="mt-1 text-xs text-slate-400">Varía por curso: Excel=3, Inglés=5, etc.</p>
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700">Plataforma de certificación</label>
        <input type="text" name="plataforma_certificacion" value="{{ old('plataforma_certificacion', $grupo->plataforma_certificacion ?? '') }}"
               placeholder="ej. Certiport" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700">Estado</label>
        <select name="estado" required class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            @foreach (['PLANIFICADO', 'EN_CURSO', 'FINALIZADO', 'CANCELADO'] as $estado)
                <option value="{{ $estado }}" {{ old('estado', $grupo->estado ?? 'PLANIFICADO') === $estado ? 'selected' : '' }}>{{ $estado }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700">Cupo máximo (vacío = sin límite)</label>
        <input type="number" min="1" name="cupo_maximo" value="{{ old('cupo_maximo', $grupo->cupo_maximo ?? '') }}"
               class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
    </div>
</div>


<button class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
    {{ $grupo->exists ? 'Guardar cambios' : 'Crear grupo' }}
</button>