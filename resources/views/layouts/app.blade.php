<!DOCTYPE html>
<html lang="es" x-data="{ sidebarOpen: false }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="{{ asset('images/icono.png') }}">
    <title>{{ $title ?? 'Dashboard' }} · FORMATEC</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-800 antialiased font-sans">
<div class="flex min-h-screen">

    {{-- Overlay para pantallas móviles --}}
    <div x-show="sidebarOpen" 
         x-transition:enter="transition-opacity ease-linear duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="sidebarOpen = false" 
         class="fixed inset-0 z-20 bg-slate-900/50 backdrop-blur-sm lg:hidden"
         style="display: none;">
    </div>

    {{-- Sidebar --}}
    <aside
        class="fixed inset-y-0 left-0 z-30 w-64 transform bg-slate-900 text-slate-100 transition-transform duration-300 ease-in-out lg:static lg:translate-x-0 shadow-xl lg:shadow-none flex flex-col justify-between"
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    >
        <div>
            {{-- Header Sidebar / Logo --}}
            <div class="flex h-16 items-center gap-3 px-6 border-b border-slate-800/80 bg-slate-950/40">
                <div class="flex h-2 w-9 items-center justify-center rounded-full bg-white text-white shadow-md shadow-emerald-500/20">
                </div>
                 <img src="{{ asset('images/formatec2024.png') }}" alt="FORMATEC" class="h-9 w-auto object-contain" >
                 <div class="flex h-2 w-9 items-center justify-center rounded-full bg-white text-white shadow-md shadow-emerald-500/20">
                </div>

            </div>

            {{-- Navegación --}}
            <nav class="px-3 py-5 space-y-1 text-sm font-medium">
                <a href="{{ route('dashboard') }}"
                   class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 transition-all duration-150 {{ request()->routeIs('dashboard') ? 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white shadow-lg shadow-emerald-600/30 font-semibold' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                    <x-nav-icon name="home" class="w-5 h-5" />
                    <span>Panel principal</span>
                </a>

                @auth
                    @php $rol = auth()->user()->rol; @endphp

                    @if (in_array($rol, ['ROOT', 'ADMINISTRADOR']))
                        <div class="pt-4 pb-1 px-3.5 flex items-center justify-between text-xs font-semibold uppercase tracking-wider text-slate-400">
                            <span>Académico</span>
                            <x-nav-icon name="chevron-down" class="w-3 h-3 opacity-60" />
                        </div>
                        
                        <a href="{{ route('cursos.index') }}"
                           class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 transition-all duration-150 {{ request()->routeIs('cursos.*') ? 'bg-slate-800 text-emerald-400 font-semibold shadow-inner' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                            <x-nav-icon name="book" class="w-5 h-5" />
                            <span>Cursos</span>
                        </a>
                        
                        <a href="{{ route('grupos.index') }}"
                           class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 transition-all duration-150 {{ request()->routeIs('grupos.*') || request()->routeIs('inscripciones.*') ? 'bg-slate-800 text-emerald-400 font-semibold shadow-inner' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                            <x-nav-icon name="folder" class="w-5 h-5" />
                            <span>Grupos</span>
                        </a>
                        
                        <a href="{{ route('totalon.index') }}"
                           class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 transition-all duration-150 {{ request()->routeIs('totalon.*') ? 'bg-slate-800 text-emerald-400 font-semibold shadow-inner' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                            <x-nav-icon name="chart" class="w-5 h-5" />
                            <span>Reporte TOTALON</span>
                        </a>

                        <div class="pt-4 pb-1 px-3.5 flex items-center justify-between text-xs font-semibold uppercase tracking-wider text-slate-400">
                            <span>Personas</span>
                            <x-nav-icon name="chevron-down" class="w-3 h-3 opacity-60" />
                        </div>
                        
                        <a href="{{ route('estudiantes.index') }}"
                           class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 transition-all duration-150 {{ request()->routeIs('estudiantes.*') ? 'bg-slate-800 text-emerald-400 font-semibold shadow-inner' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                            <x-nav-icon name="users" class="w-5 h-5" />
                            <span>Estudiantes</span>
                        </a>
                        
                        <a href="{{ route('profesores.index') }}"
                           class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 transition-all duration-150 {{ request()->routeIs('profesores.*') ? 'bg-slate-800 text-emerald-400 font-semibold shadow-inner' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                            <x-nav-icon name="user" class="w-5 h-5" />
                            <span>Profesores</span>
                        </a>
                    @endif

                    @if ($rol === 'PROFESOR')
                        <div class="pt-4 pb-1 px-3.5 flex items-center justify-between text-xs font-semibold uppercase tracking-wider text-slate-400">
                            <span>Mi trabajo</span>
                            <x-nav-icon name="chevron-down" class="w-3 h-3 opacity-60" />
                        </div>
                        
                        <a href="{{ route('grupos.index') }}"
                           class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 transition-all duration-150 {{ request()->routeIs('grupos.*') ? 'bg-slate-800 text-emerald-400 font-semibold shadow-inner' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                            <x-nav-icon name="folder" class="w-5 h-5" />
                            <span>Mis grupos</span>
                        </a>
                        
                        <a href="{{ route('estudiantes.index') }}"
                           class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 transition-all duration-150 {{ request()->routeIs('estudiantes.*') ? 'bg-slate-800 text-emerald-400 font-semibold shadow-inner' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                            <x-nav-icon name="users" class="w-5 h-5" />
                            <span>Mis estudiantes</span>
                        </a>
                    @endif

                    @if ($rol === 'ROOT')
                        <div class="pt-4 pb-1 px-3.5 flex items-center justify-between text-xs font-semibold uppercase tracking-wider text-slate-400">
                            <span>Administración</span>
                            <x-nav-icon name="chevron-down" class="w-3 h-3 opacity-60" />
                        </div>
                        
                        <a href="{{ route('usuarios.index') }}"
                           class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 transition-all duration-150 {{ request()->routeIs('usuarios.*') ? 'bg-slate-800 text-emerald-400 font-semibold shadow-inner' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                            <x-nav-icon name="user" class="w-5 h-5" />
                            <span>Usuarios y roles</span>
                        </a>
                        
                        <a href="{{ route('geografia.index') }}"
                           class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 transition-all duration-150 {{ request()->routeIs('geografia.*') ? 'bg-slate-800 text-emerald-400 font-semibold shadow-inner' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                            <x-nav-icon name="map" class="w-5 h-5" />
                            <span>Catálogo geográfico</span>
                        </a>
                        
                        <a href="{{ route('importaciones.index') }}"
                           class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 transition-all duration-150 {{ request()->routeIs('importaciones.*') ? 'bg-slate-800 text-emerald-400 font-semibold shadow-inner' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                            <x-nav-icon name="upload" class="w-5 h-5" />
                            <span>Importación masiva</span>
                        </a>
                        
                        <a href="{{ route('backups.index') }}"
                           class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 transition-all duration-150 {{ request()->routeIs('backups.*') ? 'bg-slate-800 text-emerald-400 font-semibold shadow-inner' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                            <x-nav-icon name="database" class="w-5 h-5" />
                            <span>Backups</span>
                        </a>
                    @endif
                @endauth
            </nav>
        </div>

        {{-- Footer de Sidebar (versión o estado) --}}
        <div class="p-4 border-a border-slate-900/80 text-xs text-slate-500 ">
             
        </div>
    </aside>

    {{-- Contenido principal --}}
    <div class="flex-1 flex flex-col min-w-0">
        
        {{-- Top Bar / Header --}}
        <header class="flex h-16 items-center justify-between gap-4 border-b border-slate-200/80 bg-white/90 backdrop-blur-md px-4 lg:px-8 sticky top-0 z-10">
            <div class="flex items-center gap-3">
                <button class="lg:hidden text-slate-600 hover:text-slate-900 focus:outline-none p-1.5 rounded-lg hover:bg-slate-100" @click="sidebarOpen = !sidebarOpen">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                <h1 class="text-lg font-bold text-slate-800 hidden sm:block tracking-tight">{{ $title ?? 'Panel principal' }}</h1>
            </div>

            @auth
                <form method="GET" action="{{ route('buscar.index') }}" class="flex-1 max-w-md mx-2">
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </span>
                        <input type="text" name="q" placeholder="Buscar estudiante, grupo o curso..."
                               class="w-full rounded-xl border border-slate-200 bg-slate-50/80 pl-9 pr-4 py-2 text-sm text-slate-800 placeholder-slate-400 focus:bg-white focus:border-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-400/20 transition duration-150">
                    </div>
                </form>
            @endauth

            @auth
                <div class="flex items-center gap-3 shrink-0">
                    {{-- Tarjeta/Chip del usuario con Avatar --}}
                    <div class="flex items-center gap-2.5 pl-2 pr-1 py-1 bg-slate-100/80 rounded-full border border-slate-200/60">
                        <div class="h-7 w-7 rounded-full bg-slate-800 text-white font-bold flex items-center justify-center text-xs shadow-sm">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <div class="hidden md:flex flex-col text-left pr-1">
                            <span class="text-xs font-semibold text-slate-800 leading-tight">{{ auth()->user()->name }}</span>
                            <span class="text-[10px] text-emerald-600 font-bold tracking-wider uppercase leading-tight">{{ auth()->user()->rol }}</span>
                        </div>
                        
                        <form method="POST" action="{{ route('logout') }}" class="inline-flex">
                            @csrf
                            <button type="submit" 
                                    title="Cerrar sesión"
                                    class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-full transition duration-150">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                            </button>
                        </form>
                    </div>
                </div>
            @endauth
        </header>

        {{-- Área principal de contenido --}}
        <main class="flex-1 p-4 sm:p-6 lg:p-8 max-w-7xl w-full mx-auto">
            @if (session('status'))
                <div class="mb-6 flex items-center gap-3 rounded-xl bg-emerald-50 px-4 py-3.5 text-emerald-800 border border-emerald-200/80 shadow-sm">
                    <svg class="w-5 h-5 shrink-0 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span class="text-sm font-medium">{{ session('status') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 flex items-center gap-3 rounded-xl bg-red-50 px-4 py-3.5 text-red-800 border border-red-200/80 shadow-sm">
                    <svg class="w-5 h-5 shrink-0 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span class="text-sm font-medium">{{ session('error') }}</span>
                </div>
            @endif

            {{ $slot }}
        </main>
    </div>
</div>
</body>
</html>