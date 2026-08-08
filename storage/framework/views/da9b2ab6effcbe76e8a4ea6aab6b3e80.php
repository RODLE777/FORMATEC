<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve(['title' => 'Evaluaciones'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <div class="mb-4">
        <a href="<?php echo e(route('grupos.show', ['grupo' => $grupo, 'tab' => 'estudiantes'])); ?>" class="text-sm text-slate-500 hover:underline">
            ← Volver al grupo
        </a>
        <h2 class="text-xl font-bold text-slate-900 mt-1"><?php echo e($inscripcion->estudiante->nombre_completo); ?></h2>
        <p class="text-sm text-slate-500"><?php echo e($grupo->curso->nombre); ?> · <?php echo e($grupo->codigo_grupo); ?></p>
    </div>

    <div class="mb-4 rounded-xl bg-slate-50 border border-slate-200 px-4 py-3 text-sm">
        Nota final actual:
        <span class="font-mono font-semibold"><?php echo e($inscripcion->nota_final ?? '— (aun sin evaluaciones)'); ?></span>
        <span class="text-slate-400"> — se calcula automaticamente al guardar notas, no se edita manualmente.</span>
    </div>

    <form method="POST" action="<?php echo e(route('grupos.evaluaciones.guardar', [$grupo, $inscripcion])); ?>" class="max-w-xl space-y-4">
        <?php echo csrf_field(); ?>
        <?php for($n = 1; $n <= $grupo->numero_evaluaciones; $n++): ?>
            <?php $evaluacion = $inscripcion->evaluaciones->firstWhere('numero_evaluacion', $n); ?>
            <div class="rounded-xl border border-slate-200 bg-white p-4 grid grid-cols-3 gap-3 items-end">
                <div class="col-span-2">
                    <label class="block text-xs font-medium text-slate-500">Nombre evaluacion <?php echo e($n); ?></label>
                    <input type="text" name="nombre_evaluacion[<?php echo e($n); ?>]"
                           value="<?php echo e($evaluacion->nombre_evaluacion ?? "Evaluacion {$n}"); ?>"
                           class="mt-1 w-full rounded-lg border border-slate-300 px-2 py-1.5 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500">Nota (0-10)</label>
                    <input type="number" step="0.01" min="0" max="10" name="nota[<?php echo e($n); ?>]"
                           value="<?php echo e($evaluacion->nota ?? ''); ?>"
                           class="mt-1 w-full rounded-lg border border-slate-300 px-2 py-1.5 text-sm">
                </div>
            </div>
        <?php endfor; ?>

        <button class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
            Guardar evaluaciones
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
<?php /**PATH C:\Users\UniRo\OneDrive\Escritorio\formatec-sistema-web\formatec\resources\views/grupos/evaluaciones.blade.php ENDPATH**/ ?>