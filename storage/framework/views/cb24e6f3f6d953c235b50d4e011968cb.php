<?php $grupo = $grupo ?? null; ?>

<div class="grid grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium text-slate-700">Curso</label>
        <select name="curso_id" required class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            <option value="">— Selecciona —</option>
            <?php $__currentLoopData = $cursos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($c->id); ?>" <?php echo e(old('curso_id', $grupo->curso_id ?? '') == $c->id ? 'selected' : ''); ?>><?php echo e($c->nombre); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <?php $__errorArgs = ['curso_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-red-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700">Profesor</label>
        <select name="profesor_id" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            <option value="">Sin asignar</option>
            <?php $__currentLoopData = $profesores; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($p->id); ?>" <?php echo e(old('profesor_id', $grupo->profesor_id ?? '') == $p->id ? 'selected' : ''); ?>><?php echo e($p->nombre_completo); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>
</div>

<div class="grid grid-cols-3 gap-4">
    <div>
        <label class="block text-sm font-medium text-slate-700">Codigo de grupo</label>
        <input type="text" name="codigo_grupo" value="<?php echo e(old('codigo_grupo', $grupo->codigo_grupo ?? '')); ?>" required
               placeholder="ej. EXCEL_G16" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
        <?php $__errorArgs = ['codigo_grupo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-red-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700">Año</label>
        <input type="number" name="anio" value="<?php echo e(old('anio', $grupo->anio ?? date('Y'))); ?>" required
               class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700">Mes de inicio (1-12)</label>
        <input type="number" min="1" max="12" name="mes" value="<?php echo e(old('mes', $grupo->mes ?? '')); ?>" required
               class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
    </div>
</div>

<div class="grid grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium text-slate-700">Fecha de inicio</label>
        <input type="date" name="fecha_inicio" value="<?php echo e(old('fecha_inicio', $grupo->fecha_inicio?->format('Y-m-d') ?? '')); ?>"
               class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700">Fecha de fin</label>
        <input type="date" name="fecha_fin" value="<?php echo e(old('fecha_fin', $grupo->fecha_fin?->format('Y-m-d') ?? '')); ?>"
               class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
    </div>
</div>

<div class="grid grid-cols-3 gap-4">
    <div>
        <label class="block text-sm font-medium text-slate-700">Duracion (horas)</label>
        <input type="number" name="duracion_horas" value="<?php echo e(old('duracion_horas', $grupo->duracion_horas ?? '')); ?>"
               class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700">Horario</label>
        <input type="text" name="horario" value="<?php echo e(old('horario', $grupo->horario ?? '')); ?>"
               class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700">Lugar</label>
        <input type="text" name="lugar" value="<?php echo e(old('lugar', $grupo->lugar ?? '')); ?>"
               class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
    </div>
</div>

<div class="grid grid-cols-3 gap-4">
    <div>
        <label class="block text-sm font-medium text-slate-700">Numero de evaluaciones</label>
        <input type="number" min="1" max="20" name="numero_evaluaciones"
               value="<?php echo e(old('numero_evaluaciones', $grupo->numero_evaluaciones ?? 3)); ?>" required
               class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
        <p class="mt-1 text-xs text-slate-400">Varia por curso: Excel=3, Ingles=5, etc.</p>
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700">Plataforma de certificacion</label>
        <input type="text" name="plataforma_certificacion" value="<?php echo e(old('plataforma_certificacion', $grupo->plataforma_certificacion ?? '')); ?>"
               placeholder="ej. Certiport" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700">Estado</label>
        <select name="estado" required class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            <?php $__currentLoopData = ['PLANIFICADO', 'EN_CURSO', 'FINALIZADO', 'CANCELADO']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $estado): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($estado); ?>" <?php echo e(old('estado', $grupo->estado ?? 'PLANIFICADO') === $estado ? 'selected' : ''); ?>><?php echo e($estado); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>
</div>

<button class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
    <?php echo e($grupo ? 'Guardar cambios' : 'Crear grupo'); ?>

</button>
<?php /**PATH C:\Users\UniRo\OneDrive\Escritorio\formatec-sistema-web\formatec\resources\views/grupos/_form.blade.php ENDPATH**/ ?>