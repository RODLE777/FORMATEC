<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve(['title' => 'Importacion masiva'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <div class="mb-6 max-w-xl rounded-xl bg-white p-6 shadow-sm border border-slate-100">
        <h3 class="font-semibold text-slate-900 mb-1">Importar estudiantes desde Excel</h3>
        <p class="text-sm text-slate-500 mb-4">
            Columnas esperadas en la fila 1: nombres, apellidos, sexo (MASCULINO/FEMENINO),
            fecha_nacimiento, dui, correo, telefono_celular.
        </p>
        <form method="POST" action="<?php echo e(route('importaciones.store')); ?>" enctype="multipart/form-data" class="space-y-4">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="tipo" value="ESTUDIANTES">
            <input type="file" name="archivo" accept=".xlsx,.xls,.csv" required
                   class="block w-full text-sm text-slate-600 file:mr-3 file:rounded-lg file:border-0 file:bg-slate-100 file:px-3 file:py-2 file:text-sm">
            <?php $__errorArgs = ['archivo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-red-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            <button class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                Importar
            </button>
        </form>
    </div>

    <h3 class="font-semibold text-slate-900 mb-2">Historial de importaciones</h3>
    <div class="space-y-3">
        <?php $__empty_1 = true; $__currentLoopData = $importaciones; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $importacion): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="rounded-xl bg-white p-4 border border-slate-100 shadow-sm text-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="font-medium text-slate-900"><?php echo e($importacion->archivo_nombre); ?></span>
                        <span class="text-slate-400"> · <?php echo e($importacion->created_at->format('d/m/Y H:i')); ?> · <?php echo e($importacion->usuario->name); ?></span>
                    </div>
                    <span class="rounded-full px-2 py-0.5 text-xs
                        <?php echo e($importacion->estado === 'COMPLETADO' ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700'); ?>">
                        <?php echo e($importacion->estado); ?>

                    </span>
                </div>
                <p class="mt-1 text-slate-600">
                    <?php echo e($importacion->filas_importadas); ?> de <?php echo e($importacion->total_filas); ?> filas importadas
                    <?php if($importacion->filas_error > 0): ?>
                        · <span class="text-red-600"><?php echo e($importacion->filas_error); ?> con error</span>
                    <?php endif; ?>
                </p>
                <?php if($importacion->detalle_errores): ?>
                    <details class="mt-2">
                        <summary class="cursor-pointer text-xs text-slate-500">Ver detalle de errores</summary>
                        <ul class="mt-1 text-xs text-red-600 list-disc list-inside">
                            <?php $__currentLoopData = $importacion->detalle_errores; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $err): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li>Fila <?php echo e($err['fila']); ?>: <?php echo e($err['error']); ?></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </details>
                <?php endif; ?>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <p class="text-slate-400 text-sm">Aun no se ha importado nada.</p>
        <?php endif; ?>
    </div>

    <div class="mt-4"><?php echo e($importaciones->links()); ?></div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php /**PATH C:\Users\UniRo\OneDrive\Escritorio\formatec-sistema-web\formatec\resources\views/importaciones/index.blade.php ENDPATH**/ ?>