<!DOCTYPE html>
<html lang="es" x-data="{ sidebarOpen: false }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo e($title ?? 'Dashboard'); ?> · FORMATEC</title>
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
</head>
<body class="bg-slate-50 text-slate-800 antialiased">
<div class="flex min-h-screen">

    
    <aside
        class="fixed inset-y-0 left-0 z-30 w-64 transform bg-slate-900 text-slate-100 transition-transform lg:static lg:translate-x-0"
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    >
        <div class="flex h-16 items-center gap-2 px-5 border-b border-slate-800">
            <span class="text-lg font-bold tracking-wide">FORMATEC</span>
        </div>

        <nav class="px-3 py-4 space-y-1 text-sm">
            <a href="<?php echo e(route('dashboard')); ?>"
               class="block rounded-lg px-3 py-2 hover:bg-slate-800 <?php echo e(request()->routeIs('dashboard') ? 'bg-slate-800 font-semibold' : ''); ?>">
                Panel principal
            </a>

            <?php if(auth()->guard()->check()): ?>
                <?php $rol = auth()->user()->rol; ?>

                <?php if(in_array($rol, ['ROOT', 'ADMINISTRADOR'])): ?>
                    <p class="mt-4 px-3 text-xs uppercase tracking-wider text-slate-500">Academico</p>
                    <a href="<?php echo e(route('cursos.index')); ?>"
                       class="block rounded-lg px-3 py-2 hover:bg-slate-800 <?php echo e(request()->routeIs('cursos.*') ? 'bg-slate-800 font-semibold' : ''); ?>">
                        Cursos
                    </a>
                    <a href="<?php echo e(route('grupos.index')); ?>"
                       class="block rounded-lg px-3 py-2 hover:bg-slate-800 <?php echo e(request()->routeIs('grupos.*') || request()->routeIs('inscripciones.*') ? 'bg-slate-800 font-semibold' : ''); ?>">
                        Grupos
                    </a>
                    <a href="<?php echo e(route('totalon.index')); ?>"
                       class="block rounded-lg px-3 py-2 hover:bg-slate-800 <?php echo e(request()->routeIs('totalon.*') ? 'bg-slate-800 font-semibold' : ''); ?>">
                        Reporte TOTALON
                    </a>

                    <p class="mt-4 px-3 text-xs uppercase tracking-wider text-slate-500">Personas</p>
                    <a href="<?php echo e(route('estudiantes.index')); ?>"
                       class="block rounded-lg px-3 py-2 hover:bg-slate-800 <?php echo e(request()->routeIs('estudiantes.*') ? 'bg-slate-800 font-semibold' : ''); ?>">
                        Estudiantes
                    </a>
                    <a href="<?php echo e(route('profesores.index')); ?>"
                       class="block rounded-lg px-3 py-2 hover:bg-slate-800 <?php echo e(request()->routeIs('profesores.*') ? 'bg-slate-800 font-semibold' : ''); ?>">
                        Profesores
                    </a>
                    
                <?php endif; ?>

                <?php if($rol === 'PROFESOR'): ?>
                    <p class="mt-4 px-3 text-xs uppercase tracking-wider text-slate-500">Mi trabajo</p>
                    <a href="<?php echo e(route('grupos.index')); ?>"
                       class="block rounded-lg px-3 py-2 hover:bg-slate-800 <?php echo e(request()->routeIs('grupos.*') ? 'bg-slate-800 font-semibold' : ''); ?>">
                        Mis grupos
                    </a>
                    <a href="<?php echo e(route('estudiantes.index')); ?>"
                       class="block rounded-lg px-3 py-2 hover:bg-slate-800 <?php echo e(request()->routeIs('estudiantes.*') ? 'bg-slate-800 font-semibold' : ''); ?>">
                        Mis estudiantes
                    </a>
                    
                <?php endif; ?>

                <?php if($rol === 'ROOT'): ?>
                    <p class="mt-4 px-3 text-xs uppercase tracking-wider text-slate-500">Administracion</p>
                    <a href="<?php echo e(route('usuarios.index')); ?>"
                       class="block rounded-lg px-3 py-2 hover:bg-slate-800 <?php echo e(request()->routeIs('usuarios.*') ? 'bg-slate-800 font-semibold' : ''); ?>">
                        Usuarios y roles
                    </a>
                    <a href="<?php echo e(route('geografia.index')); ?>"
                       class="block rounded-lg px-3 py-2 hover:bg-slate-800 <?php echo e(request()->routeIs('geografia.*') ? 'bg-slate-800 font-semibold' : ''); ?>">
                        Catalogo geografico
                    </a>
                    <a href="<?php echo e(route('importaciones.index')); ?>"
                       class="block rounded-lg px-3 py-2 hover:bg-slate-800 <?php echo e(request()->routeIs('importaciones.*') ? 'bg-slate-800 font-semibold' : ''); ?>">
                        Importacion masiva
                    </a>
                    <a href="<?php echo e(route('backups.index')); ?>"
                       class="block rounded-lg px-3 py-2 hover:bg-slate-800 <?php echo e(request()->routeIs('backups.*') ? 'bg-slate-800 font-semibold' : ''); ?>">
                        Backups
                    </a>
                <?php endif; ?>
            <?php endif; ?>
        </nav>
    </aside>

    <div class="flex-1 flex flex-col min-w-0">
        
        <header class="flex h-16 items-center justify-between border-b border-slate-200 bg-white px-4 lg:px-8">
            <button class="lg:hidden text-slate-600" @click="sidebarOpen = !sidebarOpen">☰</button>
            <h1 class="text-lg font-semibold"><?php echo e($title ?? 'Panel principal'); ?></h1>

            <?php if(auth()->guard()->check()): ?>
                <div class="flex items-center gap-3 text-sm">
                    <span class="text-slate-500"><?php echo e(auth()->user()->name); ?> · <?php echo e(auth()->user()->rol); ?></span>
                    <form method="POST" action="<?php echo e(route('logout')); ?>">
                        <?php echo csrf_field(); ?>
                        <button class="text-red-600 hover:underline">Salir</button>
                    </form>
                </div>
            <?php endif; ?>
        </header>

        <main class="flex-1 p-4 lg:p-8">
            <?php if(session('status')): ?>
                <div class="mb-4 rounded-lg bg-emerald-50 px-4 py-3 text-emerald-800 border border-emerald-200">
                    <?php echo e(session('status')); ?>

                </div>
            <?php endif; ?>
            <?php if(session('error')): ?>
                <div class="mb-4 rounded-lg bg-red-50 px-4 py-3 text-red-800 border border-red-200">
                    <?php echo e(session('error')); ?>

                </div>
            <?php endif; ?>

            <?php echo e($slot); ?>

        </main>
    </div>
</div>
</body>
</html>
<?php /**PATH C:\Users\UniRo\OneDrive\Escritorio\formatec-sistema-web\formatec\resources\views/layouts/app.blade.php ENDPATH**/ ?>