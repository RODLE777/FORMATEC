<?php $curso = $curso ?? null; ?>

<div>
    <label class="block text-sm font-medium text-slate-700">Nombre del curso</label>
    <input type="text" name="nombre" value="<?php echo e(old('nombre', $curso->nombre ?? '')); ?>" required
           class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
    <?php $__errorArgs = ['nombre'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-red-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
</div>

<div>
    <label class="block text-sm font-medium text-slate-700">Descripcion</label>
    <textarea name="descripcion" rows="3"
              class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"><?php echo e(old('descripcion', $curso->descripcion ?? '')); ?></textarea>
</div>

<label class="flex items-center gap-2 text-sm text-slate-700">
    <input type="checkbox" name="activo" value="1" <?php echo e(old('activo', $curso->activo ?? true) ? 'checked' : ''); ?>>
    Curso activo
</label>
<?php /**PATH C:\Users\UniRo\OneDrive\Escritorio\formatec-sistema-web\formatec\resources\views/cursos/_form.blade.php ENDPATH**/ ?>