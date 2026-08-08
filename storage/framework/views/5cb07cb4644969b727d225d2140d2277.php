<?php
    $estudiante = $estudiante ?? null;
    $encargado = $estudiante?->encargadoMenor;
    $edadInicial = $estudiante ? \Illuminate\Support\Carbon::parse($estudiante->fecha_nacimiento)->age : null;
?>

<div x-data="{
        fechaNacimiento: '<?php echo e(old('fecha_nacimiento', $estudiante->fecha_nacimiento?->format('Y-m-d') ?? '')); ?>',
        get esMenor() {
            if (!this.fechaNacimiento) return false;
            const hoy = new Date();
            const nac = new Date(this.fechaNacimiento);
            let edad = hoy.getFullYear() - nac.getFullYear();
            const m = hoy.getMonth() - nac.getMonth();
            if (m < 0 || (m === 0 && hoy.getDate() < nac.getDate())) edad--;
            return edad < 18;
        }
    }" class="space-y-6">

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-slate-700">Nombres</label>
            <input type="text" name="nombres" value="<?php echo e(old('nombres', $estudiante->nombres ?? '')); ?>" required
                   class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            <?php $__errorArgs = ['nombres'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-red-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">Apellidos</label>
            <input type="text" name="apellidos" value="<?php echo e(old('apellidos', $estudiante->apellidos ?? '')); ?>" required
                   class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            <?php $__errorArgs = ['apellidos'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-red-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-slate-700">Sexo</label>
            <select name="sexo" required class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                <option value="MASCULINO" <?php echo e(old('sexo', $estudiante->sexo ?? '') === 'MASCULINO' ? 'selected' : ''); ?>>Masculino</option>
                <option value="FEMENINO" <?php echo e(old('sexo', $estudiante->sexo ?? '') === 'FEMENINO' ? 'selected' : ''); ?>>Femenino</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">Fecha de nacimiento</label>
            <input type="date" name="fecha_nacimiento" x-model="fechaNacimiento" required
                   class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            <?php $__errorArgs = ['fecha_nacimiento'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-red-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-slate-700">DUI</label>
            <input type="text" name="dui" value="<?php echo e(old('dui', $estudiante->dui ?? '')); ?>"
                   class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            <?php $__errorArgs = ['dui'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-red-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">NIT</label>
            <input type="text" name="nit" value="<?php echo e(old('nit', $estudiante->nit ?? '')); ?>"
                   class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-slate-700">Correo</label>
            <input type="email" name="correo" value="<?php echo e(old('correo', $estudiante->correo ?? '')); ?>"
                   class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">Telefono celular</label>
            <input type="text" name="telefono_celular" value="<?php echo e(old('telefono_celular', $estudiante->telefono_celular ?? '')); ?>"
                   class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700">Direccion</label>
        <input type="text" name="direccion" value="<?php echo e(old('direccion', $estudiante->direccion ?? '')); ?>"
               class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
    </div>

    <div class="grid grid-cols-3 gap-4">
        <div>
            <label class="block text-sm font-medium text-slate-700">Departamento</label>
            <select name="departamento_id" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                <?php $__currentLoopData = $departamentos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($d->id); ?>" <?php echo e(old('departamento_id', $estudiante->departamento_id ?? '') == $d->id ? 'selected' : ''); ?>><?php echo e($d->nombre); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">Municipio</label>
            <select name="municipio_id" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                <?php $__currentLoopData = $departamentos->flatMap->municipios; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($m->id); ?>" <?php echo e(old('municipio_id', $estudiante->municipio_id ?? '') == $m->id ? 'selected' : ''); ?>><?php echo e($m->nombre); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">Distrito</label>
            <select name="distrito_id" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                <?php $__currentLoopData = $departamentos->flatMap->municipios->flatMap->distritos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $di): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($di->id); ?>" <?php echo e(old('distrito_id', $estudiante->distrito_id ?? '') == $di->id ? 'selected' : ''); ?>><?php echo e($di->nombre); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700">Comunidad / caserio / colonia</label>
        <input type="text" name="comunidad" value="<?php echo e(old('comunidad', $estudiante->comunidad ?? '')); ?>"
               class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-slate-700">Profesion / oficio</label>
            <input type="text" name="profesion_oficio" value="<?php echo e(old('profesion_oficio', $estudiante->profesion_oficio ?? '')); ?>"
                   class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">Nivel de estudio</label>
            <input type="text" name="nivel_estudio" value="<?php echo e(old('nivel_estudio', $estudiante->nivel_estudio ?? '')); ?>"
                   class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700">Enfermedades / condiciones a considerar</label>
        <textarea name="enfermedades" rows="2"
                  class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"><?php echo e(old('enfermedades', $estudiante->enfermedades ?? '')); ?></textarea>
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700">Usuario Certiport (si aplica)</label>
        <input type="text" name="usuario_certiport" value="<?php echo e(old('usuario_certiport', $estudiante->usuario_certiport ?? '')); ?>"
               class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
    </div>

    <label class="flex items-center gap-2 text-sm text-slate-700">
        <input type="checkbox" name="activo" value="1" <?php echo e(old('activo', $estudiante->activo ?? true) ? 'checked' : ''); ?>>
        Estudiante activo
    </label>

    
    <div x-show="esMenor" x-cloak class="rounded-xl border border-amber-200 bg-amber-50 p-4 space-y-3">
        <p class="text-sm font-semibold text-amber-800">Datos del encargado (obligatorio para menores de edad)</p>
        <div>
            <label class="block text-sm font-medium text-slate-700">Nombre completo del encargado</label>
            <input type="text" name="encargado_nombre_completo"
                   value="<?php echo e(old('encargado_nombre_completo', $encargado->nombre_completo ?? '')); ?>"
                   class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            <?php $__errorArgs = ['encargado_nombre_completo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-red-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700">Parentesco</label>
                <input type="text" name="encargado_parentesco"
                       value="<?php echo e(old('encargado_parentesco', $encargado->parentesco ?? '')); ?>"
                       class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700">Telefono</label>
                <input type="text" name="encargado_telefono"
                       value="<?php echo e(old('encargado_telefono', $encargado->telefono ?? '')); ?>"
                       class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            </div>
        </div>
    </div>

    <button class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
        <?php echo e($estudiante->exists ? 'Guardar cambios' : 'Registrar estudiante'); ?>

    </button>
</div><?php /**PATH C:\Users\UniRo\OneDrive\Escritorio\formatec-sistema-web\formatec\resources\views/estudiantes/_form.blade.php ENDPATH**/ ?>