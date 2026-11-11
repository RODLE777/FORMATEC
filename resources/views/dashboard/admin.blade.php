
<x-app-layout title="Centro de control">
    <div class="space-y-6 pb-10">

        {{-- ENCABEZADO --}}
        <div class="flex flex-col gap-4 xl:flex-row
                    xl:items-center xl:justify-between">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                    <span class="text-xs font-bold uppercase tracking-widest text-emerald-700">
                        Gestión académica
                    </span>
                </div>

                <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">
                    Centro de control
                </h1>

                <p class="text-sm text-slate-500 mt-1">
                    Consulta la actividad académica y gestiona FORMATEC desde un solo lugar.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <form method="GET" action="{{ url('/dashboard') }}">
                    <select
                        name="anio"
                        onchange="this.form.submit()"
                        aria-label="Seleccionar año académico"
                        class="rounded-xl border border-slate-200 bg-white
                               px-4 py-2.5 text-sm font-semibold
                               text-slate-700 focus:border-emerald-500
                               focus:ring-emerald-500"
                    >
                        @foreach($anios as $opcion)
                            <option value="{{ $opcion }}" @selected($anio == $opcion)>
                                Año {{ $opcion }}
                            </option>
                        @endforeach
                    </select>
                </form>

                <a
                    href="{{ url('/kiosco') }}"
                    class="inline-flex items-center gap-2 rounded-xl
                           bg-emerald-600 px-4 py-2.5 text-sm
                           font-semibold text-white transition
                           hover:bg-emerald-700"
                >
                    <span class="text-lg leading-none">+</span>
                    Nueva inscripción
                </a>
            </div>
        </div>

        {{-- INDICADORES --}}
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">

            <a href="{{ url('/estudiantes') }}"
               class="group rounded-2xl border border-slate-200 bg-white
                      p-5 shadow-sm transition hover:-translate-y-0.5
                      hover:border-blue-300 hover:shadow-md">
                <div class="flex items-start justify-between">
                    <div class="flex h-11 w-11 items-center justify-center
                                rounded-xl bg-blue-50 text-xl text-blue-600">
                        ♙
                    </div>
                    <span class="text-slate-400 transition group-hover:translate-x-1">
                        ↗
                    </span>
                </div>

                <p class="mt-5 text-sm font-medium text-slate-500">
                    Estudiantes registrados
                </p>

                <p class="mt-1 text-3xl font-bold text-slate-900">
                    {{ number_format($totalEstudiantes) }}
                </p>

                <p class="mt-2 text-xs text-blue-700">
                    {{ number_format($estudiantesDelAnio) }}
                    con inscripción en {{ $anio }}
                </p>
            </a>

            <a href="{{ url('/grupos') }}"
               class="group rounded-2xl border border-slate-200 bg-white
                      p-5 shadow-sm transition hover:-translate-y-0.5
                      hover:border-emerald-300 hover:shadow-md">
                <div class="flex items-start justify-between">
                    <div class="flex h-11 w-11 items-center justify-center
                                rounded-xl bg-emerald-50 text-xl">
                        📚
                    </div>
                    <span class="text-slate-400 group-hover:translate-x-1 transition">
                        ↗
                    </span>
                </div>

                <p class="mt-5 text-sm font-medium text-slate-500">
                    Grupos en curso
                </p>

                <p class="mt-1 text-3xl font-bold text-slate-900">
                    {{ $totalGruposEnCurso }}
                </p>

                <p class="mt-2 text-xs text-emerald-700">
                    {{ $gruposPlanificados }} planificados en {{ $anio }}
                </p>
            </a>

            <a href="{{ route('resultados.index', ['resultado' => 'GRADUADO']) }}"
               class="group rounded-2xl border border-slate-200 bg-white
                      p-5 shadow-sm transition hover:-translate-y-0.5
                      hover:border-violet-300 hover:shadow-md">
                <div class="flex items-start justify-between">
                    <div class="flex h-11 w-11 items-center justify-center
                                rounded-xl bg-violet-50 text-xl">
                        🎓
                    </div>
                    <span class="text-slate-400 group-hover:translate-x-1 transition">
                        ↗
                    </span>
                </div>

                <p class="mt-5 text-sm font-medium text-slate-500">
                    Graduaciones históricas
                </p>

                <p class="mt-1 text-3xl font-bold text-slate-900">
                    {{ $totalGraduados }}
                </p>

                <p class="mt-2 text-xs text-violet-700">
                    Resultados de inscripciones
                </p>
            </a>

            <a href="{{ route('resultados.index', ['resultado' => 'DESERTADO']) }}"
               class="group rounded-2xl border border-slate-200 bg-white
                      p-5 shadow-sm transition hover:-translate-y-0.5
                      hover:border-rose-300 hover:shadow-md">
                <div class="flex items-start justify-between">
                    <div class="flex h-11 w-11 items-center justify-center
                                rounded-xl bg-rose-50 text-xl">
                        ⚠
                    </div>
                    <span class="text-slate-400 group-hover:translate-x-1 transition">
                        ↗
                    </span>
                </div>

                <p class="mt-5 text-sm font-medium text-slate-500">
                    Deserciones históricas
                </p>

                <p class="mt-1 text-3xl font-bold text-slate-900">
                    {{ $totalDesertados }}
                </p>

                <p class="mt-2 text-xs text-rose-600">
                    Consultar resultados
                </p>
            </a>
        </div>

        {{-- BANDA DE RESUMEN --}}
        <div class="grid gap-3 sm:grid-cols-3">
            <div class="flex items-center justify-between rounded-xl
                        border border-slate-200 bg-white px-5 py-4">
                <div>
                    <p class="text-xs text-slate-500">Inscripciones {{ $anio }}</p>
                    <p class="mt-1 text-xl font-bold text-slate-900">
                        {{ $totalInscripcionesAnio }}
                    </p>
                </div>
                <span class="text-xl text-slate-400">✎</span>
            </div>

            <div class="flex items-center justify-between rounded-xl
                        border border-slate-200 bg-white px-5 py-4">
                <div>
                    <p class="text-xs text-slate-500">Grupos finalizados</p>
                    <p class="mt-1 text-xl font-bold text-slate-900">
                        {{ $gruposFinalizados }}
                    </p>
                </div>
                <span class="text-xl text-slate-400">✓</span>
            </div>

            <div class="flex items-center justify-between rounded-xl
                        border border-slate-200 bg-white px-5 py-4">
                <div>
                    <p class="text-xs text-slate-500">Grupos sin cupos</p>
                    <p class="mt-1 text-xl font-bold
                              {{ $gruposSinCupos > 0 ? 'text-amber-600' : 'text-slate-900' }}">
                        {{ $gruposSinCupos }}
                    </p>
                </div>
                <span class="text-xl text-slate-400">◷</span>
            </div>
        </div>

        {{-- GRÁFICAS --}}
        <div class="grid gap-5 xl:grid-cols-5">

            <section class="xl:col-span-3 rounded-2xl border
                            border-slate-200 bg-white p-5 sm:p-6 shadow-sm">
                <div class="flex flex-wrap items-start justify-between gap-3 mb-5">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">
                            Matrícula por período académico
                        </h2>
                        <p class="text-xs text-slate-500 mt-1">
                            Inscripciones según el mes asignado al grupo · {{ $anio }}
                        </p>
                    </div>

                    <span class="rounded-lg bg-slate-100 px-3 py-1.5
                                 text-xs font-semibold text-slate-600">
                        {{ $totalInscripcionesAnio }} inscripciones
                    </span>
                </div>

                <div class="relative h-64 sm:h-72">
                    <canvas id="chartMatricula"></canvas>
                </div>
            </section>

            <section class="xl:col-span-2 rounded-2xl border
                            border-slate-200 bg-white p-5 sm:p-6 shadow-sm">
                <h2 class="text-base font-bold text-slate-900">
                    Resultados académicos
                </h2>

                <p class="text-xs text-slate-500 mt-1">
                    Distribución histórica de inscripciones
                </p>

                <div class="relative mx-auto mt-4 h-48 max-w-xs">
                    <canvas id="chartResultados"></canvas>
                </div>

                <div class="mt-5 grid grid-cols-2 gap-3">
                    @php
                        $categorias = [
                            ['clave' => 'GRADUADO', 'nombre' => 'Graduados', 'color' => 'bg-emerald-500'],
                            ['clave' => 'DESERTADO', 'nombre' => 'Desertados', 'color' => 'bg-rose-500'],
                            ['clave' => 'REPROBADO', 'nombre' => 'Reprobados', 'color' => 'bg-amber-500'],
                            ['clave' => 'EN_CURSO', 'nombre' => 'En curso', 'color' => 'bg-slate-400'],
                        ];
                    @endphp

                    @foreach($categorias as $categoria)
                        <div class="rounded-xl bg-slate-50 p-3">
                            <div class="flex items-center gap-2">
                                <span class="h-2.5 w-2.5 rounded-full {{ $categoria['color'] }}"></span>
                                <span class="text-xs text-slate-500">
                                    {{ $categoria['nombre'] }}
                                </span>
                            </div>

                            <p class="mt-1 text-lg font-bold text-slate-900">
                                {{ $resultados[$categoria['clave']] }}
                            </p>
                        </div>
                    @endforeach
                </div>
            </section>
        </div>

        {{-- ACTIVIDAD Y GRUPOS --}}
        <div class="grid gap-5 xl:grid-cols-5">

            <section class="xl:col-span-3 rounded-2xl border
                            border-slate-200 bg-white shadow-sm overflow-hidden">
                <div class="flex items-center justify-between
                            gap-3 border-b border-slate-100 p-5">
                    <div>
                        <h2 class="font-bold text-slate-900">
                            Últimas inscripciones
                        </h2>
                        <p class="text-xs text-slate-500 mt-1">
                            Registros recientes de grupos del año {{ $anio }}
                        </p>
                    </div>

                    <a href="{{ url('/estudiantes') }}"
                       class="text-xs font-semibold text-emerald-700
                              hover:underline whitespace-nowrap">
                        Ver estudiantes
                    </a>
                </div>

                <div class="divide-y divide-slate-100">
                    @forelse($ultimasInscripciones as $inscripcion)
                        <div class="flex items-center gap-3 px-5 py-4
                                    hover:bg-slate-50 transition">
                            <div class="flex h-10 w-10 shrink-0 items-center
                                        justify-center rounded-xl bg-slate-100
                                        font-bold text-slate-600">
                                {{ mb_strtoupper(mb_substr($inscripcion->estudiante?->nombres ?? 'E', 0, 1)) }}
                            </div>

                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-900">
                                    {{ $inscripcion->estudiante?->nombres ?? 'Estudiante' }}
                                    {{ $inscripcion->estudiante?->apellidos ?? '' }}
                                </p>

                                <p class="truncate text-xs text-slate-500 mt-0.5">
                                    {{ $inscripcion->grupo?->curso?->nombre ?? 'Curso no disponible' }}
                                </p>
                            </div>

                            <div class="text-right shrink-0">
                                <p class="text-xs font-semibold text-slate-700">
                                    {{ $inscripcion->grupo?->codigo_grupo ?? 'Grupo' }}
                                </p>

                                <p class="text-xs text-slate-400 mt-1">
                                    {{ $inscripcion->fecha_inscripcion
                                        ? \Illuminate\Support\Carbon::parse($inscripcion->fecha_inscripcion)->format('d/m/Y')
                                        : 'Sin fecha' }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <div class="p-10 text-center">
                            <p class="text-3xl mb-2">📋</p>
                            <p class="text-sm font-semibold text-slate-700">
                                Sin inscripciones para mostrar
                            </p>
                            <p class="text-xs text-slate-500 mt-1">
                                Los nuevos registros aparecerán aquí.
                            </p>
                        </div>
                    @endforelse
                </div>
            </section>

            <section class="xl:col-span-2 rounded-2xl border
                            border-slate-200 bg-white shadow-sm overflow-hidden">
                <div class="flex items-center justify-between
                            border-b border-slate-100 p-5">
                    <div>
                        <h2 class="font-bold text-slate-900">
                            Grupos activos y planificados
                        </h2>
                        <p class="text-xs text-slate-500 mt-1">
                            Seguimiento académico
                        </p>
                    </div>

                    <a href="{{ url('/grupos') }}"
                       class="text-xs font-semibold text-emerald-700 hover:underline">
                        Ver todos
                    </a>
                </div>

                <div class="divide-y divide-slate-100">
                    @forelse($gruposRecientes as $grupo)
                        <div class="px-5 py-4">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-semibold text-slate-900">
                                        {{ $grupo->curso?->nombre ?? 'Curso' }}
                                    </p>

                                    <p class="text-xs text-slate-500 mt-1">
                                        {{ $grupo->codigo_grupo ?? 'Grupo '.$grupo->id }}
                                    </p>
                                </div>

                                <span class="shrink-0 rounded-full px-2.5 py-1
                                    text-[10px] font-bold
                                    {{ $grupo->estado === 'EN_CURSO'
                                        ? 'bg-emerald-50 text-emerald-700'
                                        : 'bg-blue-50 text-blue-700' }}">
                                    {{ $grupo->estado === 'EN_CURSO'
                                        ? 'En curso'
                                        : 'Planificado' }}
                                </span>
                            </div>

                            <div class="mt-3 flex items-center justify-between">
                                <span class="text-xs text-slate-500">
                                    Cupos disponibles
                                </span>

                                <span class="text-xs font-bold
                                    {{ $grupo->cuposDisponibles() === 0
                                        ? 'text-rose-600'
                                        : 'text-slate-700' }}">
                                    {{ $grupo->cuposDisponibles() ?? 'Sin límite' }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="p-10 text-center">
                            <p class="text-sm text-slate-500">
                                No hay grupos activos o planificados.
                            </p>
                        </div>
                    @endforelse
                </div>
            </section>
        </div>

        {{-- ACCIONES RÁPIDAS --}}
        <section class="rounded-2xl border border-slate-200
                        bg-white p-5 sm:p-6 shadow-sm">
            <div class="mb-4">
                <h2 class="font-bold text-slate-900">Accesos rápidos</h2>
                <p class="text-xs text-slate-500 mt-1">
                    Funciones de uso frecuente
                </p>
            </div>

            <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                <a href="{{ url('/kiosco') }}"
                   class="flex items-center gap-3 rounded-xl
                          border border-slate-200 p-4 transition
                          hover:border-emerald-400 hover:bg-emerald-50">
                    <span class="text-2xl">📝</span>
                    <span class="text-sm font-semibold text-slate-800">
                        Inscribir estudiante
                    </span>
                </a>

                <a href="{{ url('/grupos') }}"
                   class="flex items-center gap-3 rounded-xl
                          border border-slate-200 p-4 transition
                          hover:border-emerald-400 hover:bg-emerald-50">
                    <span class="text-2xl">📚</span>
                    <span class="text-sm font-semibold text-slate-800">
                        Gestionar grupos
                    </span>
                </a>

                <a href="{{ url('/cursos') }}"
                   class="flex items-center gap-3 rounded-xl
                          border border-slate-200 p-4 transition
                          hover:border-emerald-400 hover:bg-emerald-50">
                    <span class="text-2xl">📖</span>
                    <span class="text-sm font-semibold text-slate-800">
                        Consultar cursos
                    </span>
                </a>

                <a href="{{ url('/reportes/totalon') }}"
                   class="flex items-center gap-3 rounded-xl
                          border border-slate-200 p-4 transition
                          hover:border-emerald-400 hover:bg-emerald-50">
                    <span class="text-2xl">📊</span>
                    <span class="text-sm font-semibold text-slate-800">
                        Reporte TOTALON
                    </span>
                </a>
            </div>
        </section>
    </div>

    <script>
        (function () {
            function iniciarGraficas() {
                if (typeof Chart === 'undefined') {
                    console.warn(
                        'Chart.js no está disponible en el layout.'
                    );
                    return;
                }

                const canvasMatricula =
                    document.getElementById('chartMatricula');

                const canvasResultados =
                    document.getElementById('chartResultados');

                if (!canvasMatricula || !canvasResultados) {
                    return;
                }

                const meses = [
                    'Ene', 'Feb', 'Mar', 'Abr',
                    'May', 'Jun', 'Jul', 'Ago',
                    'Sep', 'Oct', 'Nov', 'Dic'
                ];

                const graficaAnteriorMatricula =
                    Chart.getChart(canvasMatricula);

                if (graficaAnteriorMatricula) {
                    graficaAnteriorMatricula.destroy();
                }

                const graficaAnteriorResultados =
                    Chart.getChart(canvasResultados);

                if (graficaAnteriorResultados) {
                    graficaAnteriorResultados.destroy();
                }

                new Chart(canvasMatricula, {
                    type: 'bar',
                    data: {
                        labels: meses,
                        datasets: [{
                            label: 'Inscripciones',
                            data: @json($matriculaMensual->values()),
                            backgroundColor: '#10b981',
                            hoverBackgroundColor: '#059669',
                            borderRadius: 7,
                            borderSkipped: false,
                            maxBarThickness: 32
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: '#0f172a',
                                padding: 12,
                                cornerRadius: 8
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    color: '#64748b'
                                },
                                border: {
                                    display: false
                                }
                            },
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0,
                                    color: '#64748b'
                                },
                                grid: {
                                    color: '#f1f5f9'
                                },
                                border: {
                                    display: false
                                }
                            }
                        }
                    }
                });

                new Chart(canvasResultados, {
                    type: 'doughnut',
                    data: {
                        labels: [
                            'Graduados',
                            'Desertados',
                            'Reprobados',
                            'En curso'
                        ],
                        datasets: [{
                            data: [
                                {{ $resultados['GRADUADO'] }},
                                {{ $resultados['DESERTADO'] }},
                                {{ $resultados['REPROBADO'] }},
                                {{ $resultados['EN_CURSO'] }}
                            ],
                            backgroundColor: [
                                '#10b981',
                                '#f43f5e',
                                '#f59e0b',
                                '#94a3b8'
                            ],
                            borderWidth: 3,
                            borderColor: '#ffffff',
                            hoverOffset: 5
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '76%',
                        plugins: {
                            legend: {
                                display: false
                            }
                        }
                    }
                });
            }

            if (document.readyState === 'loading') {
                document.addEventListener(
                    'DOMContentLoaded',
                    iniciarGraficas,
                    { once: true }
                );
            } else {
                iniciarGraficas();
            }
        })();
    </script>
</x-app-layout>
