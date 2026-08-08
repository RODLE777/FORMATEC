<?php $profesor = $profesor ?? null; ?>

<div class="grid grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium text-slate-700">Nombres</label>
        <input type="text" name="nombres" value="<?php echo e(old('nombres', $profesor->nombres ?? '')); ?>" required
               class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
        <?php $__errorArgs = ['nombres'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-red-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700">Apellidos</label>
        <input type="text" name="apellidos" value="<?php echo e(old('apellidos', $profesor->apellidos ?? '')); ?>" required
               class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
        <?php $__errorArgs = ['apellidos'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-red-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
</div>

<div class="grid grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium text-slate-700">DUI</label>
        <input type="text" name="dui" value="<?php echo e(old('dui', $profesor->dui ?? '')); ?>"
               class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700">Telefono</label>
        <input type="text" name="telefono" value="<?php echo e(old('telefono', $profesor->telefono ?? '')); ?>"
               class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
    </div>
</div>

<div>
    <label class="block text-sm font-medium text-slate-700">Correo</label>
    <input type="email" name="correo" value="<?php echo e(old('correo', $profesor->correo ?? '')); ?>"
           class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
</div>

<div>
    <label class="block text-sm font-medium text-slate-700">Especialidad</label>
    <input type="text" name="especialidad" value="<?php echo e(old('especialidad', $profesor->especialidad ?? '')); ?>"
           class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
</div>

<div>
    <label class="block text-sm font-medium text-slate-700">Cuenta de acceso al sistema (opcional)</label>
    <select name="user_id" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
        <option value="">— Sin cuenta vinculada —</option>
        <?php $__currentLoopData = $usuariosDisponibles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($u->id); ?>" <?php echo e(old('user_id', $profesor->user_id ?? '') == $u->id ? 'selected' : ''); ?>>
                <?php echo e($u->name); ?> (<?php echo e($u->email); ?>)
            </option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    <p class="mt-1 text-xs text-slate-400">Solo aparecen usuarios con rol PROFESOR aun no vinculados a otro profesor.</p>
</div>

<label class="flex items-center gap-2 text-sm text-slate-700">
    <input type="checkbox" name="activo" value="1" <?php echo e(old('activo', $profesor->activo ?? true) ? 'checked' : ''); ?>>
    Profesor activo
</label>
<?php /**PATH C:\Users\UniRo\OneDrive\Escritorio\formatec-sistema-web\formatec\resources\views/profesores/_form.blade.php ENDPATH**/ ?>