<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve(['title' => 'Estudiantes'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <div class="flex items-center justify-between mb-4">
        <form method="GET" class="flex gap-2">
            <input type="text" name="q" value="<?php echo e(request('q')); ?>" placeholder="Nombre o codigo FORMATEC..."
                   class="rounded-lg border border-slate-300 px-3 py-2 text-sm w-72">
            <button class="rounded-lg bg-slate-100 px-3 py-2 text-sm hover:bg-slate-200">Buscar</button>
        </form>
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create', \App\Models\Estudiante::class)): ?>
            <a href="<?php echo e(route('estudiantes.create')); ?>"
               class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                + Nuevo estudiante
            </a>
        <?php endif; ?>
    </div>

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-left text-xs uppercase text-slate-500">
                <tr>
                    <th class="px-4 py-3">Codigo</th>
                    <th class="px-4 py-3">Nombre</th>
                    <th class="px-4 py-3">Sexo</th>
                    <th class="px-4 py-3">Distrito</th>
                    <th class="px-4 py-3">Estado</th>
                    <th class="px-4 py-3 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php $__empty_1 = true; $__currentLoopData = $estudiantes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $estudiante): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td class="px-4 py-3 font-mono text-xs text-slate-500"><?php echo e($estudiante->codigo_formatec ?? '—'); ?></td>
                        <td class="px-4 py-3 font-medium text-slate-900">
                            <a href="<?php echo e(route('estudiantes.show', $estudiante)); ?>" class="hover:underline">
                                <?php echo e($estudiante->nombre_completo); ?>

                            </a>
                            <?php if($estudiante->es_menor_edad): ?>
                                <span class="ml-1 rounded-full bg-amber-50 px-2 py-0.5 text-[10px] text-amber-700">Menor</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3 text-slate-600"><?php echo e(ucfirst(strtolower($estudiante->sexo))); ?></td>
                        <td class="px-4 py-3 text-slate-600"><?php echo e($estudiante->distrito->nombre ?? '—'); ?></td>
                        <td class="px-4 py-3">
                            <span class="rounded-full px-2 py-0.5 text-xs <?php echo e($estudiante->activo ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500'); ?>">
                                <?php echo e($estudiante->activo ? 'Activo' : 'Inactivo'); ?>

                            </span>
                        </td>
                        <td class="px-4 py-3 text-right space-x-3">
                            <a href="<?php echo e(route('estudiantes.show', $estudiante)); ?>" class="text-slate-600 hover:underline">Ver</a>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $estudiante)): ?>
                                <a href="<?php echo e(route('estudiantes.edit', $estudiante)); ?>" class="text-slate-600 hover:underline">Editar</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="6" class="px-4 py-6 text-center text-slate-400">No hay estudiantes registrados.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="mt-4"><?php echo e($estudiantes->links()); ?></div>
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
<?php /**PATH C:\Users\UniRo\OneDrive\Escritorio\formatec-sistema-web\formatec\resources\views/estudiantes/index.blade.php ENDPATH**/ ?>