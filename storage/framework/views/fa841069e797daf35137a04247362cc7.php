<?php
    $tabInicial = request('tab', 'informacion');
?>

<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve(['title' => $grupo->codigo_grupo] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <div x-data="{ tab: '<?php echo e($tabInicial); ?>' }">

        <div class="mb-1 flex items-start justify-between">
            <div>
                <h2 class="text-xl font-bold text-slate-900"><?php echo e($grupo->curso->nombre); ?> — <?php echo e($grupo->codigo_grupo); ?></h2>
                <p class="text-sm text-slate-500">
                    <?php echo e($grupo->profesor?->nombre_completo ?? 'Sin profesor asignado'); ?> ·
                    <?php echo e($grupo->mes); ?>/<?php echo e($grupo->anio); ?> ·
                    <span class="rounded-full px-2 py-0.5 text-xs
                        <?php echo e($grupo->estado === 'EN_CURSO' ? 'bg-blue-50 text-blue-700' : ''); ?>

                        <?php echo e($grupo->estado === 'FINALIZADO' ? 'bg-emerald-50 text-emerald-700' : ''); ?>

                        <?php echo e($grupo->estado === 'CANCELADO' ? 'bg-red-50 text-red-700' : ''); ?>

                        <?php echo e($grupo->estado === 'PLANIFICADO' ? 'bg-slate-100 text-slate-600' : ''); ?>">
                        <?php echo e($grupo->estado); ?>

                    </span>
                </p>
            </div>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $grupo)): ?>
                <a href="<?php echo e(route('grupos.edit', $grupo)); ?>" class="rounded-lg bg-slate-100 px-4 py-2 text-sm font-semibold hover:bg-slate-200">
                    Editar informacion
                </a>
            <?php endif; ?>
        </div>

        
        <div class="mt-6 border-b border-slate-200">
            <nav class="-mb-px flex gap-6 text-sm">
                <?php $__currentLoopData = [
                    'informacion' => 'Informacion',
                    'estudiantes' => 'Estudiantes ('.$grupo->inscripciones->count().')',
                    'sesiones' => 'Sesiones ('.$grupo->sesiones->count().')',
                    'asistencia' => 'Asistencia',
                    'reportes' => 'Reportes',
                ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <button @click="tab = '<?php echo e($key); ?>'"
                            :class="tab === '<?php echo e($key); ?>' ? 'border-slate-900 text-slate-900 font-semibold' : 'border-transparent text-slate-500 hover:text-slate-700'"
                            class="border-b-2 pb-3 pt-1">
                        <?php echo e($label); ?>

                    </button>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </nav>
        </div>

        
        <div x-show="tab === 'informacion'" class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div class="rounded-xl bg-white p-5 border border-slate-100 shadow-sm text-sm space-y-2">
                <p><span class="text-slate-500">Fecha inicio:</span> <?php echo e($grupo->fecha_inicio?->format('d/m/Y') ?? '—'); ?></p>
                <p><span class="text-slate-500">Fecha fin:</span> <?php echo e($grupo->fecha_fin?->format('d/m/Y') ?? '—'); ?></p>
                <p><span class="text-slate-500">Duracion:</span> <?php echo e($grupo->duracion_horas ?? '—'); ?> horas</p>
                <p><span class="text-slate-500">Horario:</span> <?php echo e($grupo->horario ?? '—'); ?></p>
                <p><span class="text-slate-500">Lugar:</span> <?php echo e($grupo->lugar ?? '—'); ?></p>
                <p><span class="text-slate-500">Evaluaciones:</span> <?php echo e($grupo->numero_evaluaciones); ?></p>
                <p><span class="text-slate-500">Certificacion:</span> <?php echo e($grupo->plataforma_certificacion ?? '—'); ?></p>
            </div>
        </div>

        
        <div x-show="tab === 'estudiantes'" class="mt-6">
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $grupo)): ?>
                <a href="<?php echo e(route('inscripciones.create', ['grupo_id' => $grupo->id])); ?>"
                   class="mb-3 inline-block rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                    + Inscribir estudiante
                </a>
            <?php endif; ?>

            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-xs uppercase text-slate-500">
                        <tr>
                            <th class="px-4 py-3">Estudiante</th>
                            <th class="px-4 py-3">Estado</th>
                            <th class="px-4 py-3">Resultado</th>
                            <th class="px-4 py-3">Nota final</th>
                            <th class="px-4 py-3 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php $__empty_1 = true; $__currentLoopData = $grupo->inscripciones; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inscripcion): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td class="px-4 py-3 font-medium text-slate-900">
                                    <a href="<?php echo e(route('estudiantes.show', $inscripcion->estudiante)); ?>" class="hover:underline">
                                        <?php echo e($inscripcion->estudiante->nombre_completo); ?>

                                    </a>
                                </td>
                                <td class="px-4 py-3 text-slate-600"><?php echo e($inscripcion->estado); ?></td>
                                <td class="px-4 py-3 text-slate-600"><?php echo e($inscripcion->resultado_final); ?></td>
                                
                                <td class="px-4 py-3 font-mono text-slate-700"><?php echo e($inscripcion->nota_final ?? '—'); ?></td>
                                <td class="px-4 py-3 text-right space-x-3">
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $grupo)): ?>
                                        <a href="<?php echo e(route('grupos.evaluaciones.editar', [$grupo, $inscripcion])); ?>" class="text-slate-600 hover:underline">
                                            Registrar notas
                                        </a>
                                    <?php endif; ?>
                                    <?php if($inscripcion->resultado_final === 'GRADUADO'): ?>
                                        <a href="<?php echo e(route('grupos.reportes.constancia-estudiante', [$grupo, $inscripcion])); ?>" class="text-slate-600 hover:underline">
                                            Constancia
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr><td colspan="5" class="px-4 py-6 text-center text-slate-400">Aun no hay estudiantes inscritos.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        
        <div x-show="tab === 'sesiones'" class="mt-6">
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $grupo)): ?>
                <form method="POST" action="<?php echo e(route('grupos.sesiones.store', $grupo)); ?>" class="mb-4 flex flex-wrap items-end gap-2 rounded-xl border border-slate-100 bg-white p-4">
                    <?php echo csrf_field(); ?>
                    <div>
                        <label class="block text-xs font-medium text-slate-500"># Sesion</label>
                        <input type="number" name="numero_sesion" required min="1" class="mt-1 w-24 rounded-lg border border-slate-300 px-2 py-1.5 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-500">Fecha</label>
                        <input type="date" name="fecha" required class="mt-1 rounded-lg border border-slate-300 px-2 py-1.5 text-sm">
                    </div>
                    <div class="flex-1 min-w-[10rem]">
                        <label class="block text-xs font-medium text-slate-500">Tema</label>
                        <input type="text" name="tema" class="mt-1 w-full rounded-lg border border-slate-300 px-2 py-1.5 text-sm">
                    </div>
                    <button class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                        + Agregar sesion
                    </button>
                </form>
            <?php endif; ?>

            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-xs uppercase text-slate-500">
                        <tr>
                            <th class="px-4 py-3">#</th>
                            <th class="px-4 py-3">Fecha</th>
                            <th class="px-4 py-3">Tema</th>
                            <th class="px-4 py-3 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php $__empty_1 = true; $__currentLoopData = $grupo->sesiones; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sesion): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td class="px-4 py-3"><?php echo e($sesion->numero_sesion); ?></td>
                                <td class="px-4 py-3 text-slate-600"><?php echo e($sesion->fecha->format('d/m/Y')); ?></td>
                                <td class="px-4 py-3 text-slate-600"><?php echo e($sesion->tema ?? '—'); ?></td>
                                <td class="px-4 py-3 text-right">
                                    <a href="<?php echo e(route('grupos.sesiones.pasar-lista', [$grupo, $sesion])); ?>" class="text-slate-600 hover:underline">
                                        Pasar lista
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr><td colspan="4" class="px-4 py-6 text-center text-slate-400">Aun no hay sesiones registradas.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        
        <div x-show="tab === 'asistencia'" class="mt-6">
            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-xs uppercase text-slate-500">
                        <tr>
                            <th class="px-4 py-3">Estudiante</th>
                            <?php $__currentLoopData = $grupo->sesiones; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sesion): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <th class="px-3 py-3 text-center">S<?php echo e($sesion->numero_sesion); ?></th>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <th class="px-4 py-3 text-center">% Asistencia</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php $__empty_1 = true; $__currentLoopData = $grupo->inscripciones; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inscripcion): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td class="px-4 py-3 font-medium text-slate-900"><?php echo e($inscripcion->estudiante->nombre_completo); ?></td>
                                <?php $__currentLoopData = $grupo->sesiones; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sesion): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $asistio = $inscripcion->asistencias->firstWhere('sesion_id', $sesion->id)?->asistio;
                                    ?>
                                    <td class="px-3 py-3 text-center">
                                        <?php if($asistio === null): ?> <span class="text-slate-300">·</span>
                                        <?php elseif($asistio): ?> <span class="text-emerald-600">✓</span>
                                        <?php else: ?> <span class="text-red-500">✕</span>
                                        <?php endif; ?>
                                    </td>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <td class="px-4 py-3 text-center font-mono"><?php echo e($inscripcion->porcentajeAsistencia() ?? '—'); ?>%</td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr><td colspan="99" class="px-4 py-6 text-center text-slate-400">Sin datos aun.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <p class="mt-2 text-xs text-slate-400">Para registrar asistencia de una sesion especifica, entra a la pestaña Sesiones y usa "Pasar lista".</p>
        </div>

        
        <div x-show="tab === 'reportes'" class="mt-6 grid gap-4 sm:grid-cols-2">
            <a href="<?php echo e(route('grupos.reportes.notas', $grupo)); ?>"
               class="rounded-xl bg-white p-5 border border-slate-100 shadow-sm hover:border-slate-300">
                <p class="font-semibold text-slate-900">Acta de notas</p>
                <p class="text-sm text-slate-500 mt-1">PDF con las evaluaciones y nota final de cada estudiante del grupo.</p>
            </a>
            <a href="<?php echo e(route('grupos.reportes.asistencia', $grupo)); ?>"
               class="rounded-xl bg-white p-5 border border-slate-100 shadow-sm hover:border-slate-300">
                <p class="font-semibold text-slate-900">Constancia de asistencia</p>
                <p class="text-sm text-slate-500 mt-1">PDF con el detalle de asistencia por sesion de todo el grupo.</p>
            </a>
            <div class="rounded-xl bg-slate-50 p-5 border border-slate-100 sm:col-span-2">
                <p class="text-sm text-slate-500">
                    Para la constancia individual de un estudiante graduado, entra a la pestaña
                    "Estudiantes" y usa "Constancia" junto a su nombre.
                </p>
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
<?php /**PATH C:\Users\UniRo\OneDrive\Escritorio\formatec-sistema-web\formatec\resources\views/grupos/show.blade.php ENDPATH**/ ?>