<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve(['title' => 'Panel principal'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-xl bg-white p-5 shadow-sm border border-slate-100">
            <p class="text-sm text-slate-500">Estudiantes registrados</p>
            <p class="mt-1 text-3xl font-bold text-slate-900"><?php echo e($totalEstudiantes); ?></p>
        </div>
        <div class="rounded-xl bg-white p-5 shadow-sm border border-slate-100">
            <p class="text-sm text-slate-500">Grupos en curso</p>
            <p class="mt-1 text-3xl font-bold text-slate-900"><?php echo e($totalGruposEnCurso); ?></p>
        </div>
        <div class="rounded-xl bg-white p-5 shadow-sm border border-slate-100">
            <p class="text-sm text-slate-500">Graduados (historico)</p>
            <p class="mt-1 text-3xl font-bold text-emerald-600"><?php echo e($totalGraduados); ?></p>
        </div>
        <div class="rounded-xl bg-white p-5 shadow-sm border border-slate-100">
            <p class="text-sm text-slate-500">Desertados (historico)</p>
            <p class="mt-1 text-3xl font-bold text-red-500"><?php echo e($totalDesertados); ?></p>
        </div>
    </div>

    <div class="mt-8 rounded-xl bg-white p-5 shadow-sm border border-slate-100">
        <p class="text-sm text-slate-500">
            El detalle de matricula/desercion/graduacion por curso y periodo (TOTALON),
            asistencia y notas se activa en las siguientes fases del proyecto.
        </p>
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
<?php /**PATH C:\Users\UniRo\OneDrive\Escritorio\formatec-sistema-web\formatec\resources\views/dashboard/admin.blade.php ENDPATH**/ ?>