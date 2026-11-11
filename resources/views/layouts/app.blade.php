
<!DOCTYPE html>
<html lang="es" x-data="{ sidebarOpen: false }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon"
          type="image/png"
          href="{{ asset('images/icono.png') }}">

    <title>{{ $title ?? 'Dashboard' }} · FORMATEC</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-50 text-slate-800 antialiased font-sans">

<div class="min-h-screen">

    {{-- OVERLAY MÓVIL --}}
    <div
        x-show="sidebarOpen"
        x-cloak
        x-transition.opacity
        @click="sidebarOpen = false"
        class="fixed inset-0 z-40 bg-slate-950/60 backdrop-blur-sm lg:hidden">
    </div>

    {{-- MENÚ LATERAL FIJO --}}
    <aside
        class="fixed inset-y-0 left-0 z-50 flex w-64 flex-col
               bg-slate-900 text-slate-100 shadow-xl
               transform transition-transform duration-300 ease-in-out
               -translate-x-full lg:translate-x-0"
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
        aria-label="Navegación principal">

        {{-- LOGO --}}
        <div class="flex h-16 shrink-0 items-center justify-center
                    gap-3 border-b border-slate-800 bg-slate-950/40 px-5">

            <span class="h-2 w-7 rounded-full bg-white"></span>

            <img
                src="{{ asset('images/formatec2024.png') }}"
                alt="FORMATEC"
                class="h-9 w-auto max-w-[150px] object-contain">

            <span class="h-2 w-7 rounded-full bg-white"></span>

            <button
                type="button"
                @click="sidebarOpen = false"
                class="absolute right-2 top-4 rounded-lg p-2
                       text-slate-400 hover:bg-slate-800
                       hover:text-white lg:hidden"
                aria-label="Cerrar menú">

                <svg class="h-5 w-5"
                     fill="none"
                     stroke="currentColor"
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- NAVEGACIÓN CON SCROLL INDEPENDIENTE --}}
        <nav class="min-h-0 flex-1 overflow-y-auto px-3 py-5
                    space-y-1 text-sm font-medium">

            {{-- PANEL PRINCIPAL --}}
            <a
                href="{{ route('dashboard') }}"
                @click="sidebarOpen = false"
                class="flex items-center gap-3 rounded-xl
                       px-3.5 py-2.5 transition-all duration-150
                       {{ request()->routeIs('dashboard')
                           ? 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white shadow-lg shadow-emerald-600/20 font-semibold'
                           : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">

                <x-nav-icon name="home" class="h-5 w-5"/>

                <span>Panel principal</span>
            </a>

            @auth
                @php
                    $rol = auth()->user()->rol;
                @endphp

                {{-- ADMINISTRADOR Y ROOT --}}
                @if(in_array($rol, ['ROOT', 'ADMINISTRADOR']))

                    <div class="flex items-center justify-between
                                px-3.5 pb-1 pt-5 text-xs font-semibold
                                uppercase tracking-wider text-slate-400">
                        <span>Académico</span>
                        <x-nav-icon name="chevron-down"
                                    class="h-3 w-3 opacity-60"/>
                    </div>

                    <a
                        href="{{ route('cursos.index') }}"
                        @click="sidebarOpen = false"
                        class="flex items-center gap-3 rounded-xl px-3.5 py-2.5
                               transition-all duration-150
                               {{ request()->routeIs('cursos.*')
                                   ? 'bg-slate-800 text-emerald-400 font-semibold'
                                   : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">

                        <x-nav-icon name="book" class="h-5 w-5"/>
                        <span>Cursos</span>
                    </a>

                    <a
                        href="{{ route('grupos.index') }}"
                        @click="sidebarOpen = false"
                        class="flex items-center gap-3 rounded-xl px-3.5 py-2.5
                               transition-all duration-150
                               {{ request()->routeIs('grupos.*') || request()->routeIs('inscripciones.*')
                                   ? 'bg-slate-800 text-emerald-400 font-semibold'
                                   : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">

                        <x-nav-icon name="folder" class="h-5 w-5"/>
                        <span>Grupos</span>
                    </a>

                    <a
                        href="{{ route('totalon.index') }}"
                        @click="sidebarOpen = false"
                        class="flex items-center gap-3 rounded-xl px-3.5 py-2.5
                               transition-all duration-150
                               {{ request()->routeIs('totalon.*')
                                   ? 'bg-slate-800 text-emerald-400 font-semibold'
                                   : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">

                        <x-nav-icon name="chart" class="h-5 w-5"/>
                        <span>Reporte TOTALON</span>
                    </a>

                    {{-- PERSONAS --}}
                    <div class="flex items-center justify-between
                                px-3.5 pb-1 pt-5 text-xs font-semibold
                                uppercase tracking-wider text-slate-400">
                        <span>Personas</span>
                        <x-nav-icon name="chevron-down"
                                    class="h-3 w-3 opacity-60"/>
                    </div>

                    <a
                        href="{{ route('estudiantes.index') }}"
                        @click="sidebarOpen = false"
                        class="flex items-center gap-3 rounded-xl px-3.5 py-2.5
                               transition-all duration-150
                               {{ request()->routeIs('estudiantes.*')
                                   ? 'bg-slate-800 text-emerald-400 font-semibold'
                                   : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">

                        <x-nav-icon name="users" class="h-5 w-5"/>
                        <span>Estudiantes</span>
                    </a>

                    <a
                        href="{{ route('profesores.index') }}"
                        @click="sidebarOpen = false"
                        class="flex items-center gap-3 rounded-xl px-3.5 py-2.5
                               transition-all duration-150
                               {{ request()->routeIs('profesores.*')
                                   ? 'bg-slate-800 text-emerald-400 font-semibold'
                                   : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">

                        <x-nav-icon name="user" class="h-5 w-5"/>
                        <span>Profesores</span>
                    </a>

                @endif

                {{-- PROFESOR --}}
                @if($rol === 'PROFESOR')

                    <div class="flex items-center justify-between
                                px-3.5 pb-1 pt-5 text-xs font-semibold
                                uppercase tracking-wider text-slate-400">
                        <span>Mi trabajo</span>
                        <x-nav-icon name="chevron-down"
                                    class="h-3 w-3 opacity-60"/>
                    </div>

                    <a
                        href="{{ route('grupos.index') }}"
                        @click="sidebarOpen = false"
                        class="flex items-center gap-3 rounded-xl px-3.5 py-2.5
                               transition-all duration-150
                               {{ request()->routeIs('grupos.*')
                                   ? 'bg-slate-800 text-emerald-400 font-semibold'
                                   : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">

                        <x-nav-icon name="folder" class="h-5 w-5"/>
                        <span>Mis grupos</span>
                    </a>

                    <a
                        href="{{ route('estudiantes.index') }}"
                        @click="sidebarOpen = false"
                        class="flex items-center gap-3 rounded-xl px-3.5 py-2.5
                               transition-all duration-150
                               {{ request()->routeIs('estudiantes.*')
                                   ? 'bg-slate-800 text-emerald-400 font-semibold'
                                   : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">

                        <x-nav-icon name="users" class="h-5 w-5"/>
                        <span>Mis estudiantes</span>
                    </a>

                @endif

                {{-- SOLO ROOT --}}
                @if($rol === 'ROOT')

                    <div class="flex items-center justify-between
                                px-3.5 pb-1 pt-5 text-xs font-semibold
                                uppercase tracking-wider text-slate-400">
                        <span>Administración</span>
                        <x-nav-icon name="chevron-down"
                                    class="h-3 w-3 opacity-60"/>
                    </div>

                    <a
                        href="{{ route('usuarios.index') }}"
                        @click="sidebarOpen = false"
                        class="flex items-center gap-3 rounded-xl px-3.5 py-2.5
                               transition-all duration-150
                               {{ request()->routeIs('usuarios.*')
                                   ? 'bg-slate-800 text-emerald-400 font-semibold'
                                   : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">

                        <x-nav-icon name="user" class="h-5 w-5"/>
                        <span>Usuarios y roles</span>
                    </a>

                    <a
                        href="{{ route('backups.index') }}"
                        @click="sidebarOpen = false"
                        class="flex items-center gap-3 rounded-xl px-3.5 py-2.5
                               transition-all duration-150
                               {{ request()->routeIs('backups.*')
                                   ? 'bg-slate-800 text-emerald-400 font-semibold'
                                   : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">

                        <x-nav-icon name="database" class="h-5 w-5"/>
                        <span>Backups</span>
                    </a>

                    <a
                        href="{{ route('kiosco.index') }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="flex items-center gap-3 rounded-xl px-3.5 py-2.5
                               text-slate-300 transition-all duration-150
                               hover:bg-slate-800/70 hover:text-white">

                        <x-nav-icon name="user" class="h-5 w-5"/>
                        <span>Kiosco de registro</span>
                    </a>

                @endif

            @endauth
        </nav>

        {{-- PIE DEL MENÚ --}}
        <div class="shrink-0 border-t border-slate-800 px-4 py-3">
            <div class="flex items-center gap-2">
                <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                <span class="text-xs text-slate-400">
                    Sistema académico FORMATEC
                </span>
            </div>
        </div>
    </aside>

    {{-- CONTENIDO PRINCIPAL --}}
    {{-- lg:pl-64 reserva el espacio del sidebar fijo --}}
    <div class="flex min-h-screen min-w-0 flex-col lg:pl-64">

        {{-- BARRA SUPERIOR --}}
        <header
            class="sticky top-0 z-30 flex h-16 shrink-0
                   items-center justify-between gap-3
                   border-b border-slate-200/80
                   bg-white/95 px-4 backdrop-blur-md
                   sm:gap-4 lg:px-8">

            {{-- TÍTULO Y BOTÓN MÓVIL --}}
            <div class="flex min-w-0 items-center gap-3">

                <button
                    type="button"
                    @click="sidebarOpen = !sidebarOpen"
                    :aria-expanded="sidebarOpen.toString()"
                    aria-label="Abrir menú"
                    class="rounded-lg p-2 text-slate-600
                           hover:bg-slate-100 hover:text-slate-900
                           lg:hidden">

                    <svg class="h-6 w-6"
                         fill="none"
                         stroke="currentColor"
                         viewBox="0 0 24 24">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>

                <h1 class="hidden truncate text-lg font-bold
                           tracking-tight text-slate-800 sm:block">
                    {{ $title ?? 'Panel principal' }}
                </h1>
            </div>

            {{-- BÚSQUEDA GLOBAL --}}
            @auth
                <form
                    method="GET"
                    action="{{ route('buscar.index') }}"
                    class="mx-1 min-w-0 max-w-md flex-1 sm:mx-2">

                    <div class="relative">

                        <span class="pointer-events-none absolute inset-y-0
                                     left-0 flex items-center pl-3 text-slate-400">

                            <svg class="h-4 w-4"
                                 fill="none"
                                 stroke="currentColor"
                                 viewBox="0 0 24 24">

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </span>

                        <input
                            type="search"
                            name="q"
                            placeholder="Buscar estudiante, grupo o curso..."
                            aria-label="Búsqueda global"
                            class="w-full rounded-xl border border-slate-200
                                   bg-slate-50/80 py-2 pl-9 pr-4
                                   text-sm text-slate-800
                                   placeholder-slate-400
                                   transition duration-150
                                   focus:border-emerald-500
                                   focus:bg-white
                                   focus:outline-none
                                   focus:ring-2 focus:ring-emerald-500/20">
                    </div>
                </form>
            @endauth

            {{-- USUARIO Y CIERRE DE SESIÓN --}}
            @auth
                <div class="flex shrink-0 items-center gap-3">

                    <div class="flex items-center gap-2.5 rounded-full
                                border border-slate-200/60
                                bg-slate-100/80 py-1 pl-2 pr-1">

                        <div class="flex h-7 w-7 items-center justify-center
                                    rounded-full bg-slate-800
                                    text-xs font-bold text-white shadow-sm">
                            {{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                        </div>

                        <div class="hidden flex-col pr-1 text-left md:flex">

                            <span class="text-xs font-semibold leading-tight
                                         text-slate-800">
                                {{ auth()->user()->name }}
                            </span>

                            <span class="text-[10px] font-bold uppercase
                                         leading-tight tracking-wider
                                         text-emerald-600">
                                {{ auth()->user()->rol }}
                            </span>
                        </div>

                        <form
                            method="POST"
                            action="{{ route('logout') }}"
                            class="inline-flex">

                            @csrf

                            <button
                                type="submit"
                                title="Cerrar sesión"
                                aria-label="Cerrar sesión"
                                class="rounded-full p-1.5 text-slate-400
                                       transition duration-150
                                       hover:bg-red-50 hover:text-red-600">

                                <svg class="h-4 w-4"
                                     fill="none"
                                     stroke="currentColor"
                                     viewBox="0 0 24 24">

                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            @endauth
        </header>

        {{-- CONTENIDO DE LAS PÁGINAS --}}
        <main class="mx-auto w-full max-w-7xl flex-1
                     p-4 sm:p-6 lg:p-8">

            {{-- MENSAJE DE ÉXITO --}}
            @if(session('status'))
                <div class="mb-6 flex items-center gap-3 rounded-xl
                            border border-emerald-200
                            bg-emerald-50 px-4 py-3.5
                            text-emerald-800 shadow-sm">

                    <svg class="h-5 w-5 shrink-0 text-emerald-600"
                         fill="none"
                         stroke="currentColor"
                         viewBox="0 0 24 24">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>

                    <span class="text-sm font-medium">
                        {{ session('status') }}
                    </span>
                </div>
            @endif

            {{-- MENSAJE DE ERROR --}}
            @if(session('error'))
                <div class="mb-6 flex items-center gap-3 rounded-xl
                            border border-red-200
                            bg-red-50 px-4 py-3.5
                            text-red-800 shadow-sm">

                    <svg class="h-5 w-5 shrink-0 text-red-600"
                         fill="none"
                         stroke="currentColor"
                         viewBox="0 0 24 24">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>

                    <span class="text-sm font-medium">
                        {{ session('error') }}
                    </span>
                </div>
            @endif

            {{ $slot }}

        </main>
    </div>
</div>

</body>
</html>
