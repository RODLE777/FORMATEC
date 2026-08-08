<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve(['title' => 'Reporte TOTALON'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <div class="mb-4 flex flex-wrap items-end justify-between gap-3">
        <form method="GET" class="flex gap-2 items-end">
            <div>
                <label class="block text-xs font-medium text-slate-500">Año</label>
                <select name="anio" onchange="this.form.submit()" class="mt-1 rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    <?php $__currentLoopData = $aniosDisponibles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($a); ?>" <?php echo e($anio == $a ? 'selected' : ''); ?>><?php echo e($a); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500">Periodo</label>
                <select name="periodo" onchange="this.form.submit()" class="mt-1 rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    <option value="anual" <?php echo e($periodo === 'anual' ? 'selected' : ''); ?>>Consolidado anual</option>
                    <option value="mensual" <?php echo e($periodo === 'mensual' ? 'selected' : ''); ?>>Detalle mensual</option>
                </select>
            </div>
        </form>

        <a href="<?php echo e(route('totalon.pdf', ['anio' => $anio, 'periodo' => $periodo])); ?>"
           class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
            Descargar PDF
        </a>
    </div>

    <p class="mb-3 text-sm text-slate-500">
        Matricula, desercion y graduacion por curso — <?php echo e($periodo === 'mensual' ? 'detalle mensual' : 'consolidado anual'); ?> <?php echo e($anio); ?>.
        Este es el unico reporte que cruza varios grupos; el resto de reportes se genera desde cada grupo individual.
    </p>

    <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-left text-xs uppercase text-slate-500">
                <tr>
                    <th class="px-3 py-3">Curso</th>
                    <?php if($periodo === 'mensual'): ?> <th class="px-3 py-3">Mes</th> <?php endif; ?>
                    <th class="px-3 py-3 text-center"># Grupos</th>
                    <th class="px-3 py-3 text-center">Iniciaron M</th>
                    <th class="px-3 py-3 text-center">Iniciaron F</th>
                    <th class="px-3 py-3 text-center">Iniciaron Total</th>
                    <th class="px-3 py-3 text-center">Desertaron</th>
                    <th class="px-3 py-3 text-center">Graduados</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php $__empty_1 = true; $__currentLoopData = $filas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fila): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td class="px-3 py-3 font-medium text-slate-900"><?php echo e($fila->curso); ?></td>
                        <?php if($periodo === 'mensual'): ?> <td class="px-3 py-3 text-slate-600"><?php echo e($fila->mes); ?></td> <?php endif; ?>
                        <td class="px-3 py-3 text-center"><?php echo e($fila->numero_de_cursos); ?></td>
                        <td class="px-3 py-3 text-center"><?php echo e($fila->iniciaron_masculino); ?></td>
                        <td class="px-3 py-3 text-center"><?php echo e($fila->iniciaron_femenino); ?></td>
                        <td class="px-3 py-3 text-center font-semibold"><?php echo e($fila->iniciaron_total); ?></td>
                        <td class="px-3 py-3 text-center text-red-600"><?php echo e($fila->desertados_total); ?></td>
                        <td class="px-3 py-3 text-center text-emerald-600"><?php echo e($fila->graduados_total); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="8" class="px-3 py-6 text-center text-slate-400">Sin datos para este periodo.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
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
<?php /**PATH C:\Users\UniRo\OneDrive\Escritorio\formatec-sistema-web\formatec\resources\views/reportes/totalon.blade.php ENDPATH**/ ?>