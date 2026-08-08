<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Helvetica, Arial, sans-serif; font-size: 10px; color: #1e293b; }
        h1 { font-size: 16px; margin-bottom: 2px; }
        p.sub { color: #64748b; margin-top: 0; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #cbd5e1; padding: 5px 6px; text-align: left; }
        th { background: #f1f5f9; text-transform: uppercase; font-size: 8px; color: #475569; }
        .center { text-align: center; }
        .footer { margin-top: 24px; font-size: 9px; color: #94a3b8; }
    </style>
</head>
<body>
    <h1>Constancia de asistencia — <?php echo e($grupo->curso->nombre); ?></h1>
    <p class="sub">Grupo <?php echo e($grupo->codigo_grupo); ?> · <?php echo e($grupo->mes); ?>/<?php echo e($grupo->anio); ?></p>

    <table>
        <thead>
            <tr>
                <th>Estudiante</th>
                <?php $__currentLoopData = $grupo->sesiones; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sesion): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <th class="center">S<?php echo e($sesion->numero_sesion); ?><br><?php echo e($sesion->fecha->format('d/m')); ?></th>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <th class="center">%</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $grupo->inscripciones; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inscripcion): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($inscripcion->estudiante->nombre_completo); ?></td>
                    <?php $__currentLoopData = $grupo->sesiones; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sesion): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php $asistio = $inscripcion->asistencias->firstWhere('sesion_id', $sesion->id)?->asistio; ?>
                        <td class="center"><?php echo e($asistio === null ? '—' : ($asistio ? 'P' : 'A')); ?></td>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <td class="center"><?php echo e($inscripcion->porcentajeAsistencia() ?? '—'); ?>%</td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>

    <p class="footer">P = presente, A = ausente. Generado el <?php echo e(now()->format('d/m/Y H:i')); ?> — FORMATEC, Cuscatlan Sur.</p>
</body>
</html>
<?php /**PATH C:\Users\UniRo\OneDrive\Escritorio\formatec-sistema-web\formatec\resources\views/reportes/constancia-asistencia.blade.php ENDPATH**/ ?>