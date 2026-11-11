
<x-app-layout title="Mi panel académico">
    <div class="space-y-6 pb-10">

        {{-- ENCABEZADO --}}
        <div class="flex flex-col gap-3 sm:flex-row
                    sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-widest
                          text-emerald-700 mb-2">
                    Espacio del docente
                </p>

                <h1 class="text-2xl sm:text-3xl font-bold text-slate-900">
                    Mi panel académico
                </h1>

                <p class="text-sm text-slate-500 mt-1">
                    Consulta tus grupos, estudiantes y actividad académica.
                </p>
            </div>

            <div class="rounded-xl bg-white border border-slate-200
                        px-4 py-3 text-sm text-slate-600">
                {{ now()->translatedFormat('d \d\e F \d\e Y') }}
            </div>
        </div>

        {{-- RESUMEN --}}
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-2xl border border-slate-200
                        bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-slate-500">
                        Mis grupos
                    </span>
                    <span class="text-xl">📚</span>
                </div>

                <p class="mt-4 text-3xl font-bold text-slate-900">
                    {{ $totalGrupos }}
                </p>

                <p class="text-xs text-slate-500 mt-2">
                    Grupos asignados
                </p>
            </div>

            <div class="rounded-2xl border border-slate-200
                        bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-slate-500">
                        En curso
                    </span>
                    <span class="text-xl">🟢</span>
                </div>

                <p class="mt-4 text-3xl font-bold text-emerald-600">
                    {{ $gruposEnCurso }}
                </p>

                <p class="text-xs text-slate-500 mt-2">
                    {{ $gruposPlanificados }} por iniciar
                </p>
            </div>

            <div class="rounded-2xl border border-slate-200
                        bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-slate-500">
                        Estudiantes a mi cargo
                    </span>
                    <span class="text-xl">👥</span>
                </div>

                <p class="mt-4 text-3xl font-bold text-slate-900">
                    {{ $totalEstudiantes }}
                </p>

                <p class="text-xs text-slate-500 mt-2">
                    Estudiantes únicos
                </p>
            </div>

            <div class="rounded-2xl border border-slate-200
                        bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-slate-500">
                        Inscripciones
                    </span>
                    <span class="text-xl">📋</span>
                </div>

                <p class="mt-4 text-3xl font-bold text-slate-900">
                    {{ $totalInscripciones }}
                </p>

                <p class="text-xs text-slate-500 mt-2">
                    En todos mis grupos
                </p>
            </div>
        </div>

        {{-- MIS GRUPOS --}}
        <section class="rounded-2xl border border-slate-200
                        bg-white shadow-sm overflow-hidden">
            <div class="border-b border-slate-100 p-5 sm:p-6">
                <h2 class="text-lg font-bold text-slate-900">
                    Mis grupos activos y planificados
                </h2>

                <p class="text-sm text-slate-500 mt-1">
                    Consulta el estado y los cupos disponibles
                    de tus grupos.
                </p>
            </div>

            <div class="grid gap-4 p-5 sm:grid-cols-2 xl:grid-cols-3">
                @forelse($misGrupos as $grupo)
                    <div class="rounded-xl border border-slate-200
                                p-5 transition hover:border-emerald-300
                                hover:shadow-sm">
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-xs font-bold
                                text-emerald-700 uppercase">
                                {{ $grupo->codigo_grupo ?? 'Grupo '.$grupo->id }}
                            </span>

                            <span class="rounded-full px-2.5 py-1 text-xs font-bold
                                {{ $grupo->estado === 'EN_CURSO'
                                    ? 'bg-emerald-50 text-emerald-700'
                                    : 'bg-blue-50 text-blue-700' }}">
                                {{ $grupo->estado === 'EN_CURSO'
                                    ? 'En curso'
                                    : 'Planificado' }}
                            </span>
                        </div>

                        <h3 class="mt-4 text-base font-bold
                                   text-slate-900">
                            {{ $grupo->curso?->nombre ?? 'Curso' }}
                        </h3>

                        <p class="text-sm text-slate-500 mt-2">
                            {{ $grupo->horario ?? 'Horario por confirmar' }}
                        </p>

                        <div class="mt-5 pt-4 border-t border-slate-100
                                    flex items-center justify-between">
                            <span class="text-xs text-slate-500">
                                Cupos disponibles
                            </span>

                            <span class="text-sm font-bold text-slate-900">
                                {{ $grupo->cuposDisponibles() ?? 'Sin límite' }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="sm:col-span-2 xl:col-span-3
                                py-10 text-center">
                        <p class="text-3xl mb-3">📚</p>

                        <p class="font-semibold text-slate-700">
                            No tienes grupos activos por el momento
                        </p>

                        <p class="text-sm text-slate-500 mt-2">
                            Tus grupos aparecerán aquí cuando
                            sean asignados.
                        </p>
                    </div>
                @endforelse
            </div>
        </section>

        {{-- INSCRIPCIONES RECIENTES --}}
        <section class="rounded-2xl border border-slate-200
                        bg-white shadow-sm overflow-hidden">
            <div class="p-5 sm:p-6 border-b border-slate-100">
                <h2 class="text-lg font-bold text-slate-900">
                    Inscripciones recientes
                </h2>

                <p class="text-sm text-slate-500 mt-1">
                    Últimos estudiantes inscritos en tus grupos.
                </p>
            </div>

            <div class="divide-y divide-slate-100">
                @forelse($ultimasInscripciones as $inscripcion)
                    <div class="flex items-center gap-3
                                px-5 sm:px-6 py-4 hover:bg-slate-50">
                        <div class="flex h-11 w-11 shrink-0
                                    items-center justify-center
                                    rounded-xl bg-emerald-50
                                    font-bold text-emerald-700">
                            {{ mb_strtoupper(mb_substr(
                                $inscripcion->estudiante?->nombres ?? 'E',
                                0,
                                1
                            )) }}
                        </div>

                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-semibold
                                      text-slate-900">
                                {{ $inscripcion->estudiante?->nombres ?? 'Estudiante' }}
                                {{ $inscripcion->estudiante?->apellidos ?? '' }}
                            </p>

                            <p class="truncate text-xs text-slate-500 mt-1">
                                {{ $inscripcion->grupo?->curso?->nombre ?? 'Curso' }}
                            </p>
                        </div>

                        <div class="shrink-0 text-right">
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
                    <div class="py-12 text-center text-sm text-slate-500">
                        Todavía no hay inscripciones para mostrar.
                    </div>
                @endforelse
            </div>
        </section>
    </div>
</x-app-layout>
