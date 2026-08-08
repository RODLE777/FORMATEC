<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Helvetica, Arial, sans-serif; font-size: 11px; color: #1e293b; }
        h1 { font-size: 16px; margin-bottom: 2px; }
        p.sub { color: #64748b; margin-top: 0; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #cbd5e1; padding: 6px 8px; text-align: left; }
        th { background: #f1f5f9; text-transform: uppercase; font-size: 9px; color: #475569; }
        .center { text-align: center; }
        .footer { margin-top: 24px; font-size: 9px; color: #94a3b8; }
    </style>
</head>
<body>
    <h1>Acta de notas — <?php echo e($grupo->curso->nombre); ?></h1>
    <p class="sub">
        Grupo <?php echo e($grupo->codigo_grupo); ?> · <?php echo e($grupo->mes); ?>/<?php echo e($grupo->anio); ?> ·
        Profesor: <?php echo e($grupo->profesor?->nombre_completo ?? 'Sin asignar'); ?>

    </p>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Estudiante</th>
                <?php for($n = 1; $n <= $grupo->numero_evaluaciones; $n++): ?>
                    <th class="center">Eval. <?php echo e($n); ?></th>
                <?php endfor; ?>
                <th class="center">Nota final</th>
                <th class="center">Resultado</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $grupo->inscripciones; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $inscripcion): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($i + 1); ?></td>
                    <td><?php echo e($inscripcion->estudiante->nombre_completo); ?></td>
                    <?php for($n = 1; $n <= $grupo->numero_evaluaciones; $n++): ?>
                        <td class="center"><?php echo e($inscripcion->evaluaciones->firstWhere('numero_evaluacion', $n)->nota ?? '—'); ?></td>
                    <?php endfor; ?>
                    <td class="center"><strong><?php echo e($inscripcion->nota_final ?? '—'); ?></strong></td>
                    <td class="center"><?php echo e($inscripcion->resultado_final); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>

    <p class="footer">Generado el <?php echo e(now()->format('d/m/Y H:i')); ?> — FORMATEC, Cuscatlan Sur.</p>
</body>
</html>
<?php /**PATH C:\Users\UniRo\OneDrive\Escritorio\formatec-sistema-web\formatec\resources\views/reportes/acta-notas.blade.php ENDPATH**/ ?>