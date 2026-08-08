<!DOCTYPE html>
<html lang="es" x-data="{ sidebarOpen: false }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Dashboard' }} · FORMATEC</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-800 antialiased">
<div class="flex min-h-screen">

    {{-- Sidebar --}}
    <aside
        class="fixed inset-y-0 left-0 z-30 w-64 transform bg-slate-900 text-slate-100 transition-transform lg:static lg:translate-x-0"
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    >
        <div class="flex h-16 items-center gap-2 px-5 border-b border-slate-800">
            <span class="text-lg font-bold tracking-wide">FORMATEC</span>
        </div>

        <nav class="px-3 py-4 space-y-1 text-sm">
            <a href="{{ route('dashboard') }}"
               class="block rounded-lg px-3 py-2 hover:bg-slate-800 {{ request()->routeIs('dashboard') ? 'bg-slate-800 font-semibold' : '' }}">
                Panel principal
            </a>

            @auth
                @php $rol = auth()->user()->rol; @endphp

                @if (in_array($rol, ['ROOT', 'ADMINISTRADOR']))
                    <p class="mt-4 px-3 text-xs uppercase tracking-wider text-slate-500">Academico</p>
                    <a href="{{ route('cursos.index') }}"
                       class="block rounded-lg px-3 py-2 hover:bg-slate-800 {{ request()->routeIs('cursos.*') ? 'bg-slate-800 font-semibold' : '' }}">
                        Cursos
                    </a>
                    <a href="{{ route('grupos.index') }}"
                       class="block rounded-lg px-3 py-2 hover:bg-slate-800 {{ request()->routeIs('grupos.*') || request()->routeIs('inscripciones.*') ? 'bg-slate-800 font-semibold' : '' }}">
                        Grupos
                    </a>
                    <a href="{{ route('totalon.index') }}"
                       class="block rounded-lg px-3 py-2 hover:bg-slate-800 {{ request()->routeIs('totalon.*') ? 'bg-slate-800 font-semibold' : '' }}">
                        Reporte TOTALON
                    </a>

                    <p class="mt-4 px-3 text-xs uppercase tracking-wider text-slate-500">Personas</p>
                    <a href="{{ route('estudiantes.index') }}"
                       class="block rounded-lg px-3 py-2 hover:bg-slate-800 {{ request()->routeIs('estudiantes.*') ? 'bg-slate-800 font-semibold' : '' }}">
                        Estudiantes
                    </a>
                    <a href="{{ route('profesores.index') }}"
                       class="block rounded-lg px-3 py-2 hover:bg-slate-800 {{ request()->routeIs('profesores.*') ? 'bg-slate-800 font-semibold' : '' }}">
                        Profesores
                    </a>
                    {{-- Sesiones, asistencia, evaluaciones y reportes: viven DENTRO
                    de cada Grupo (pestañas), no como modulos independientes. --}}
                @endif

                @if ($rol === 'PROFESOR')
                    <p class="mt-4 px-3 text-xs uppercase tracking-wider text-slate-500">Mi trabajo</p>
                    <a href="{{ route('grupos.index') }}"
                       class="block rounded-lg px-3 py-2 hover:bg-slate-800 {{ request()->routeIs('grupos.*') ? 'bg-slate-800 font-semibold' : '' }}">
                        Mis grupos
                    </a>
                    <a href="{{ route('estudiantes.index') }}"
                       class="block rounded-lg px-3 py-2 hover:bg-slate-800 {{ request()->routeIs('estudiantes.*') ? 'bg-slate-800 font-semibold' : '' }}">
                        Mis estudiantes
                    </a>
                    {{-- Pasar lista y registrar notas se hacen desde dentro de cada grupo. --}}
                @endif

                @if ($rol === 'ROOT')
                    <p class="mt-4 px-3 text-xs uppercase tracking-wider text-slate-500">Administracion</p>
                    <a href="{{ route('usuarios.index') }}"
                       class="block rounded-lg px-3 py-2 hover:bg-slate-800 {{ request()->routeIs('usuarios.*') ? 'bg-slate-800 font-semibold' : '' }}">
                        Usuarios y roles
                    </a>
                    <a href="{{ route('geografia.index') }}"
                       class="block rounded-lg px-3 py-2 hover:bg-slate-800 {{ request()->routeIs('geografia.*') ? 'bg-slate-800 font-semibold' : '' }}">
                        Catalogo geografico
                    </a>
                    <a href="{{ route('importaciones.index') }}"
                       class="block rounded-lg px-3 py-2 hover:bg-slate-800 {{ request()->routeIs('importaciones.*') ? 'bg-slate-800 font-semibold' : '' }}">
                        Importacion masiva
                    </a>
                    <a href="{{ route('backups.index') }}"
                       class="block rounded-lg px-3 py-2 hover:bg-slate-800 {{ request()->routeIs('backups.*') ? 'bg-slate-800 font-semibold' : '' }}">
                        Backups
                    </a>
                @endif
            @endauth
        </nav>
    </aside>

    <div class="flex-1 flex flex-col min-w-0">
        {{-- Top bar --}}
        <header class="flex h-16 items-center justify-between border-b border-slate-200 bg-white px-4 lg:px-8">
            <button class="lg:hidden text-slate-600" @click="sidebarOpen = !sidebarOpen">☰</button>
            <h1 class="text-lg font-semibold">{{ $title ?? 'Panel principal' }}</h1>

            @auth
                <div class="flex items-center gap-3 text-sm">
                    <span class="text-slate-500">{{ auth()->user()->name }} · {{ auth()->user()->rol }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="text-red-600 hover:underline">Salir</button>
                    </form>
                </div>
            @endauth
        </header>

        <main class="flex-1 p-4 lg:p-8">
            @if (session('status'))
                <div class="mb-4 rounded-lg bg-emerald-50 px-4 py-3 text-emerald-800 border border-emerald-200">
                    {{ session('status') }}
                </div>
            @endif
            @if (session('error'))
                <div class="mb-4 rounded-lg bg-red-50 px-4 py-3 text-red-800 border border-red-200">
                    {{ session('error') }}
                </div>
            @endif

            {{ $slot }}
        </main>
    </div>
</div>
</body>
</html>
