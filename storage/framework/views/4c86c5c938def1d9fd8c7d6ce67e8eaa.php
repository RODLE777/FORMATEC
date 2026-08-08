<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve(['title' => 'Ficha del estudiante'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <div class="mb-4 flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-900"><?php echo e($estudiante->nombre_completo); ?></h2>
            <p class="text-sm text-slate-500 font-mono"><?php echo e($estudiante->codigo_formatec); ?></p>
        </div>
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $estudiante)): ?>
            <a href="<?php echo e(route('estudiantes.edit', $estudiante)); ?>"
               class="rounded-lg bg-slate-100 px-4 py-2 text-sm font-semibold hover:bg-slate-200">Editar ficha</a>
        <?php endif; ?>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-1 space-y-4">
            <div class="rounded-xl bg-white p-5 border border-slate-100 shadow-sm text-sm space-y-2">
                <p><span class="text-slate-500">Sexo:</span> <?php echo e(ucfirst(strtolower($estudiante->sexo))); ?></p>
                <p><span class="text-slate-500">Edad:</span> <?php echo e($estudiante->edad); ?> años
                    <?php if($estudiante->es_menor_edad): ?>
                        <span class="rounded-full bg-amber-50 px-2 py-0.5 text-[10px] text-amber-700">Menor de edad</span>
                    <?php endif; ?>
                </p>
                <p><span class="text-slate-500">DUI:</span> <?php echo e($estudiante->dui ?? '—'); ?></p>
                <p><span class="text-slate-500">Correo:</span> <?php echo e($estudiante->correo ?? '—'); ?></p>
                <p><span class="text-slate-500">Celular:</span> <?php echo e($estudiante->telefono_celular ?? '—'); ?></p>
                <p><span class="text-slate-500">Direccion:</span> <?php echo e($estudiante->direccion ?? '—'); ?></p>
                <p><span class="text-slate-500">Distrito:</span> <?php echo e($estudiante->distrito->nombre ?? '—'); ?>, <?php echo e($estudiante->municipio->nombre ?? ''); ?></p>
            </div>

            <?php if($estudiante->es_menor_edad): ?>
                <div class="rounded-xl bg-amber-50 border border-amber-200 p-5 text-sm space-y-1">
                    <p class="font-semibold text-amber-800">Encargado</p>
                    <p><?php echo e($estudiante->encargadoMenor?->nombre_completo ?? 'No registrado'); ?></p>
                    <p class="text-amber-700"><?php echo e($estudiante->encargadoMenor?->parentesco); ?> · <?php echo e($estudiante->encargadoMenor?->telefono); ?></p>
                </div>
            <?php endif; ?>
        </div>

        <div class="lg:col-span-2">
            <h3 class="font-semibold text-slate-900 mb-2">Historial de cursos</h3>
            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-xs uppercase text-slate-500">
                        <tr>
                            <th class="px-4 py-3">Curso / grupo</th>
                            <th class="px-4 py-3">Periodo</th>
                            <th class="px-4 py-3">Resultado</th>
                            <th class="px-4 py-3">Nota final</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php $__empty_1 = true; $__currentLoopData = $estudiante->inscripciones; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inscripcion): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td class="px-4 py-3 font-medium text-slate-900">
                                    <a href="<?php echo e(route('grupos.show', $inscripcion->grupo)); ?>" class="hover:underline">
                                        <?php echo e($inscripcion->grupo->curso->nombre); ?>

                                        <span class="text-slate-400 text-xs">(<?php echo e($inscripcion->grupo->codigo_grupo); ?>)</span>
                                    </a>
                                </td>
                                <td class="px-4 py-3 text-slate-600"><?php echo e($inscripcion->grupo->mes); ?>/<?php echo e($inscripcion->grupo->anio); ?></td>
                                <td class="px-4 py-3">
                                    <span class="rounded-full px-2 py-0.5 text-xs
                                        <?php echo e($inscripcion->resultado_final === 'GRADUADO' ? 'bg-emerald-50 text-emerald-700' : ''); ?>

                                        <?php echo e($inscripcion->resultado_final === 'DESERTADO' ? 'bg-red-50 text-red-700' : ''); ?>

                                        <?php echo e($inscripcion->resultado_final === 'REPROBADO' ? 'bg-red-50 text-red-700' : ''); ?>

                                        <?php echo e($inscripcion->resultado_final === 'EN_CURSO' ? 'bg-slate-100 text-slate-600' : ''); ?>">
                                        <?php echo e($inscripcion->resultado_final); ?>

                                    </span>
                                </td>
                                
                                <td class="px-4 py-3 font-mono text-slate-700"><?php echo e($inscripcion->nota_final ?? '—'); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr><td colspan="4" class="px-4 py-6 text-center text-slate-400">Sin cursos registrados aun.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
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
<?php /**PATH C:\Users\UniRo\OneDrive\Escritorio\formatec-sistema-web\formatec\resources\views/estudiantes/show.blade.php ENDPATH**/ ?>