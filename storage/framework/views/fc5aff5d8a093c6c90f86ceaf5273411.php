<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve(['title' => 'Pasar lista'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <div class="mb-4">
        <a href="<?php echo e(route('grupos.show', ['grupo' => $grupo, 'tab' => 'sesiones'])); ?>" class="text-sm text-slate-500 hover:underline">
            ← Volver al grupo
        </a>
        <h2 class="text-xl font-bold text-slate-900 mt-1">
            Sesion <?php echo e($sesion->numero_sesion); ?> — <?php echo e($sesion->fecha->format('d/m/Y')); ?>

        </h2>
        <p class="text-sm text-slate-500"><?php echo e($grupo->curso->nombre); ?> · <?php echo e($grupo->codigo_grupo); ?> <?php if($sesion->tema): ?> · <?php echo e($sesion->tema); ?> <?php endif; ?></p>
    </div>

    <form method="POST" action="<?php echo e(route('grupos.sesiones.asistencia.guardar', [$grupo, $sesion])); ?>">
        <?php echo csrf_field(); ?>
        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs uppercase text-slate-500">
                    <tr>
                        <th class="px-4 py-3">Estudiante</th>
                        <th class="px-4 py-3 text-center">Asistio</th>
                        <th class="px-4 py-3">Observacion</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php $__empty_1 = true; $__currentLoopData = $inscripciones; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inscripcion): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php $registro = $inscripcion->asistencias->first(); ?>
                        <tr>
                            <td class="px-4 py-3 font-medium text-slate-900"><?php echo e($inscripcion->estudiante->nombre_completo); ?></td>
                            <td class="px-4 py-3 text-center">
                                <input type="checkbox" name="asistencia[<?php echo e($inscripcion->id); ?>]" value="1"
                                       <?php echo e($registro?->asistio ? 'checked' : ''); ?> class="h-4 w-4">
                            </td>
                            <td class="px-4 py-3">
                                <input type="text" name="observacion[<?php echo e($inscripcion->id); ?>]"
                                       value="<?php echo e($registro?->observacion); ?>"
                                       class="w-full rounded-lg border border-slate-300 px-2 py-1 text-sm">
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="3" class="px-4 py-6 text-center text-slate-400">No hay estudiantes activos en este grupo.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <button class="mt-4 rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
            Guardar asistencia
        </button>
    </form>
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
<?php /**PATH C:\Users\UniRo\OneDrive\Escritorio\formatec-sistema-web\formatec\resources\views/grupos/pasar-lista.blade.php ENDPATH**/ ?>