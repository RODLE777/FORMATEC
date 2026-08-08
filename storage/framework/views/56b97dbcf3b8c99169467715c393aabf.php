<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve(['title' => 'Profesores'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <div class="flex items-center justify-between mb-4">
        <form method="GET" class="flex gap-2">
            <input type="text" name="q" value="<?php echo e(request('q')); ?>" placeholder="Buscar profesor..."
                   class="rounded-lg border border-slate-300 px-3 py-2 text-sm w-64">
            <button class="rounded-lg bg-slate-100 px-3 py-2 text-sm hover:bg-slate-200">Buscar</button>
        </form>
        <a href="<?php echo e(route('profesores.create')); ?>"
           class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
            + Nuevo profesor
        </a>
    </div>

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-left text-xs uppercase text-slate-500">
                <tr>
                    <th class="px-4 py-3">Nombre</th>
                    <th class="px-4 py-3">Especialidad</th>
                    <th class="px-4 py-3">Cuenta vinculada</th>
                    <th class="px-4 py-3">Estado</th>
                    <th class="px-4 py-3 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php $__empty_1 = true; $__currentLoopData = $profesores; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $profesor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td class="px-4 py-3 font-medium text-slate-900"><?php echo e($profesor->nombre_completo); ?></td>
                        <td class="px-4 py-3 text-slate-600"><?php echo e($profesor->especialidad ?? '—'); ?></td>
                        <td class="px-4 py-3 text-slate-600"><?php echo e($profesor->user?->email ?? 'Sin cuenta'); ?></td>
                        <td class="px-4 py-3">
                            <span class="rounded-full px-2 py-0.5 text-xs <?php echo e($profesor->activo ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500'); ?>">
                                <?php echo e($profesor->activo ? 'Activo' : 'Inactivo'); ?>

                            </span>
                        </td>
                        <td class="px-4 py-3 text-right space-x-3">
                            <a href="<?php echo e(route('profesores.edit', $profesor)); ?>" class="text-slate-600 hover:underline">Editar</a>
                            <form method="POST" action="<?php echo e(route('profesores.destroy', $profesor)); ?>" class="inline"
                                  onsubmit="return confirm('¿Eliminar este profesor?')">
                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                <button class="text-red-600 hover:underline">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="5" class="px-4 py-6 text-center text-slate-400">No hay profesores registrados.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="mt-4"><?php echo e($profesores->links()); ?></div>
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
<?php /**PATH C:\Users\UniRo\OneDrive\Escritorio\formatec-sistema-web\formatec\resources\views/profesores/index.blade.php ENDPATH**/ ?>