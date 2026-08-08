<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve(['title' => 'Backups'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <div class="mb-6 flex items-center justify-between rounded-xl bg-white p-6 shadow-sm border border-slate-100">
        <div>
            <h3 class="font-semibold text-slate-900">Respaldo de base de datos</h3>
            <p class="text-sm text-slate-500 mt-1">
                Genera un volcado completo (estructura, datos, triggers y vistas) via mysqldump.
            </p>
        </div>
        <form method="POST" action="<?php echo e(route('backups.store')); ?>">
            <?php echo csrf_field(); ?>
            <button class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                Generar backup ahora
            </button>
        </form>
    </div>

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-left text-xs uppercase text-slate-500">
                <tr>
                    <th class="px-4 py-3">Archivo</th>
                    <th class="px-4 py-3">Tipo</th>
                    <th class="px-4 py-3">Tamaño</th>
                    <th class="px-4 py-3">Generado por</th>
                    <th class="px-4 py-3">Fecha</th>
                    <th class="px-4 py-3">Estado</th>
                    <th class="px-4 py-3 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php $__empty_1 = true; $__currentLoopData = $backups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $backup): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td class="px-4 py-3 font-mono text-xs"><?php echo e($backup->nombre_archivo); ?></td>
                        <td class="px-4 py-3 text-slate-600"><?php echo e($backup->tipo); ?></td>
                        <td class="px-4 py-3 text-slate-600">
                            <?php echo e($backup->tamano_bytes ? round($backup->tamano_bytes / 1024, 1).' KB' : '—'); ?>

                        </td>
                        <td class="px-4 py-3 text-slate-600"><?php echo e($backup->usuario->name); ?></td>
                        <td class="px-4 py-3 text-slate-600"><?php echo e($backup->created_at->format('d/m/Y H:i')); ?></td>
                        <td class="px-4 py-3">
                            <span class="rounded-full px-2 py-0.5 text-xs <?php echo e($backup->estado === 'COMPLETADO' ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700'); ?>">
                                <?php echo e($backup->estado); ?>

                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <?php if($backup->estado === 'COMPLETADO'): ?>
                                <a href="<?php echo e(route('backups.download', $backup)); ?>" class="text-slate-600 hover:underline">Descargar</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="7" class="px-4 py-6 text-center text-slate-400">Aun no se ha generado ningun backup.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="mt-4"><?php echo e($backups->links()); ?></div>

    <p class="mt-4 text-xs text-slate-400">
        La restauracion de un backup es una operacion delicada (sobrescribe la base de datos actual)
        y se realiza manualmente por el equipo tecnico, no desde esta pantalla, para evitar perdidas
        accidentales de informacion.
    </p>
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
<?php /**PATH C:\Users\UniRo\OneDrive\Escritorio\formatec-sistema-web\formatec\resources\views/backups/index.blade.php ENDPATH**/ ?>