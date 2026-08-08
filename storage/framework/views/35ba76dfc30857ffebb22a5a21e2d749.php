<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve(['title' => 'Grupos'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
        <form method="GET" class="flex gap-2">
            <select name="curso_id" onchange="this.form.submit()" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
                <option value="">Todos los cursos</option>
                <?php $__currentLoopData = $cursos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($c->id); ?>" <?php echo e(request('curso_id') == $c->id ? 'selected' : ''); ?>><?php echo e($c->nombre); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <input type="number" name="anio" value="<?php echo e(request('anio')); ?>" placeholder="Año"
                   class="w-24 rounded-lg border border-slate-300 px-3 py-2 text-sm">
            <button class="rounded-lg bg-slate-100 px-3 py-2 text-sm hover:bg-slate-200">Filtrar</button>
        </form>
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create', \App\Models\Grupo::class)): ?>
            <a href="<?php echo e(route('grupos.create')); ?>"
               class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                + Nuevo grupo
            </a>
        <?php endif; ?>
    </div>

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-left text-xs uppercase text-slate-500">
                <tr>
                    <th class="px-4 py-3">Grupo</th>
                    <th class="px-4 py-3">Curso</th>
                    <th class="px-4 py-3">Profesor</th>
                    <th class="px-4 py-3">Periodo</th>
                    <th class="px-4 py-3">Inscritos</th>
                    <th class="px-4 py-3">Estado</th>
                    <th class="px-4 py-3 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php $__empty_1 = true; $__currentLoopData = $grupos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $grupo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td class="px-4 py-3 font-medium text-slate-900">
                            <a href="<?php echo e(route('grupos.show', $grupo)); ?>" class="hover:underline"><?php echo e($grupo->codigo_grupo); ?></a>
                        </td>
                        <td class="px-4 py-3 text-slate-600"><?php echo e($grupo->curso->nombre); ?></td>
                        <td class="px-4 py-3 text-slate-600"><?php echo e($grupo->profesor?->nombre_completo ?? 'Sin asignar'); ?></td>
                        <td class="px-4 py-3 text-slate-600"><?php echo e($grupo->mes); ?>/<?php echo e($grupo->anio); ?></td>
                        <td class="px-4 py-3 text-slate-600"><?php echo e($grupo->inscripciones_count); ?></td>
                        <td class="px-4 py-3">
                            <span class="rounded-full px-2 py-0.5 text-xs
                                <?php echo e($grupo->estado === 'EN_CURSO' ? 'bg-blue-50 text-blue-700' : ''); ?>

                                <?php echo e($grupo->estado === 'FINALIZADO' ? 'bg-emerald-50 text-emerald-700' : ''); ?>

                                <?php echo e($grupo->estado === 'CANCELADO' ? 'bg-red-50 text-red-700' : ''); ?>

                                <?php echo e($grupo->estado === 'PLANIFICADO' ? 'bg-slate-100 text-slate-600' : ''); ?>">
                                <?php echo e($grupo->estado); ?>

                            </span>
                        </td>
                        <td class="px-4 py-3 text-right space-x-3">
                            <a href="<?php echo e(route('grupos.show', $grupo)); ?>" class="text-slate-600 hover:underline">Ver</a>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $grupo)): ?>
                                <a href="<?php echo e(route('grupos.edit', $grupo)); ?>" class="text-slate-600 hover:underline">Editar</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="7" class="px-4 py-6 text-center text-slate-400">No hay grupos registrados.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="mt-4"><?php echo e($grupos->links()); ?></div>
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
<?php /**PATH C:\Users\UniRo\OneDrive\Escritorio\formatec-sistema-web\formatec\resources\views/grupos/index.blade.php ENDPATH**/ ?>