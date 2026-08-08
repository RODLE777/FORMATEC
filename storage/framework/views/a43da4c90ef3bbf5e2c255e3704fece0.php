<?php $usuario = $usuario ?? null; ?>

<div>
    <label class="block text-sm font-medium text-slate-700">Nombre completo</label>
    <input type="text" name="name" value="<?php echo e(old('name', $usuario->name ?? '')); ?>" required
           class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
    <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-red-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
</div>

<div>
    <label class="block text-sm font-medium text-slate-700">Correo</label>
    <input type="email" name="email" value="<?php echo e(old('email', $usuario->email ?? '')); ?>" required
           class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
    <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-red-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
</div>

<div class="grid grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium text-slate-700">
            Contrasena <?php echo e($usuario ? '(dejar vacio para no cambiar)' : ''); ?>

        </label>
        <input type="password" name="password" <?php echo e($usuario ? '' : 'required'); ?>

               class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
        <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-red-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
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
        <?php $__currentLoopData = ['ROOT', 'ADMINISTRADOR', 'PROFESOR']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rol): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($rol); ?>" <?php echo e(old('rol', $usuario->rol ?? '') === $rol ? 'selected' : ''); ?>><?php echo e($rol); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
</div>

<label class="flex items-center gap-2 text-sm text-slate-700">
    <input type="checkbox" name="activo" value="1" <?php echo e(old('activo', $usuario->activo ?? true) ? 'checked' : ''); ?>>
    Cuenta activa
</label>
<?php /**PATH C:\Users\UniRo\OneDrive\Escritorio\formatec-sistema-web\formatec\resources\views/usuarios/_form.blade.php ENDPATH**/ ?>