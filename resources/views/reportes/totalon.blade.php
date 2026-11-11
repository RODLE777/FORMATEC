
<x-app-layout title="Reporte TOTALON">
    @php
        $campos = [
            'numero_de_cursos',
            'iniciaron_masculino',
            'iniciaron_femenino',
            'iniciaron_total',
            'desertados_masculino',
            'desertados_femenino',
            'desertados_total',
            'graduados_masculino',
            'graduados_femenino',
            'graduados_total',
        ];

        $enCurso = $gruposExcluidos
            ->where('estado', 'EN_CURSO');

        $planificados = $gruposExcluidos
            ->where('estado', 'PLANIFICADO');

        $cancelados = $gruposExcluidos
            ->where('estado', 'CANCELADO');

        $otrosExcluidos = $gruposExcluidos
            ->where('estado', 'FINALIZADO');

        $pendientes = $advertencias
            ->where('tipo', 'resultado_pendiente');

        $errores = $advertencias
            ->where('tipo', '!=', 'resultado_pendiente');

        $advertenciasPorGrupo = $advertencias
            ->groupBy('grupo_id');
    @endphp

    <div class="space-y-6">

        {{-- ENCABEZADO --}}
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">
                    Reporte TOTALON
                </h1>

                <p class="mt-1 text-sm text-slate-500">
                    Informe institucional consolidado de grupos finalizados.
                </p>
            </div>

            <div class="rounded-xl bg-slate-900 px-4 py-3 text-white">
                <p class="text-xs uppercase tracking-wide text-slate-300">
                    Período seleccionado
                </p>

                <p class="font-semibold">
                    {{ $nombrePeriodo }}
                </p>
            </div>
        </div>

        {{-- ERRORES --}}
        @if ($errors->any())
            <div class="rounded-xl border border-red-200 bg-red-50 p-4">
                <p class="font-semibold text-red-800">
                    Revisa los filtros del reporte.
                </p>

                <ul class="mt-2 list-disc pl-5 text-sm text-red-700">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- FILTROS --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="mb-4 text-base font-bold text-slate-800">
                Filtros del informe
            </h2>

            <form
                method="GET"
                action="{{ route('totalon.index') }}"
                class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-5"
            >
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">
                        Año
                    </label>

                    <select
                        name="anio"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                    >
                        @foreach ($aniosDisponibles as $a)
                            <option
                                value="{{ $a }}"
                                @selected($anio == $a)
                            >
                                {{ $a }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">
                        Mes inicial
                    </label>

                    <select
                        name="mes_inicio"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                    >
                        @foreach ($meses as $numero => $nombre)
                            <option
                                value="{{ $numero }}"
                                @selected($mesInicio == $numero)
                            >
                                {{ $nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">
                        Mes final
                    </label>

                    <select
                        name="mes_fin"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                    >
                        @foreach ($meses as $numero => $nombre)
                            <option
                                value="{{ $numero }}"
                                @selected($mesFin == $numero)
                            >
                                {{ $nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">
                        Curso
                    </label>

                    <select
                        name="curso_id"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                    >
                        <option value="">
                            Todos los cursos
                        </option>

                        @foreach ($cursos as $curso)
                            <option
                                value="{{ $curso->id }}"
                                @selected($cursoId == $curso->id)
                            >
                                {{ $curso->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-end">
                    <button
                        type="submit"
                        class="w-full rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-700"
                    >
                        Generar reporte
                    </button>
                </div>
            </form>

            <p class="mt-3 text-xs text-slate-500">
                Selecciona al menos dos meses del mismo año.
                Los grupos se contabilizan según su fecha de finalización.
            </p>
        </div>

        {{-- RESUMEN --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm text-slate-500">
                    Grupos finalizados
                </p>

                <p class="mt-2 text-3xl font-bold text-slate-900">
                    {{ $totales->numero_de_cursos }}
                </p>

                <p class="mt-1 text-xs text-slate-500">
                    Incluidos en el TOTALON
                </p>
            </div>

            <div class="rounded-2xl border border-blue-200 bg-blue-50 p-5">
                <p class="text-sm font-medium text-blue-700">
                    Participantes que iniciaron
                </p>

                <p class="mt-2 text-3xl font-bold text-blue-900">
                    {{ $totales->iniciaron_total }}
                </p>

                <p class="mt-1 text-xs text-blue-700">
                    M: {{ $totales->iniciaron_masculino }}
                    |
                    F: {{ $totales->iniciaron_femenino }}
                </p>
            </div>

            <div class="rounded-2xl border border-orange-200 bg-orange-50 p-5">
                <p class="text-sm font-medium text-orange-700">
                    Desertados
                </p>

                <p class="mt-2 text-3xl font-bold text-orange-900">
                    {{ $totales->desertados_total }}
                </p>

                <p class="mt-1 text-xs text-orange-700">
                    M: {{ $totales->desertados_masculino }}
                    |
                    F: {{ $totales->desertados_femenino }}
                </p>
            </div>

            <div class="rounded-2xl border border-green-200 bg-green-50 p-5">
                <p class="text-sm font-medium text-green-700">
                    Graduados
                </p>

                <p class="mt-2 text-3xl font-bold text-green-900">
                    {{ $totales->graduados_total }}
                </p>

                <p class="mt-1 text-xs text-green-700">
                    M: {{ $totales->graduados_masculino }}
                    |
                    F: {{ $totales->graduados_femenino }}
                </p>
            </div>
        </div>

        {{-- EXPORTACIONES --}}
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-lg font-bold text-slate-900">
                    Consolidado por curso
                </h2>

                <p class="text-sm text-slate-500">
                    Resultados separados por género y totales generales.
                </p>
            </div>

            <div class="flex flex-wrap gap-2">
                <a
                    href="{{ route('totalon.pdf', $filtros) }}"
                    class="rounded-lg bg-red-700 px-4 py-2 text-sm font-semibold text-white hover:bg-red-800"
                >
                    Descargar PDF
                </a>

                <a
                    href="{{ route('totalon.excel', $filtros) }}"
                    class="rounded-lg bg-emerald-700 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-800"
                >
                    Descargar Excel
                </a>
            </div>
        </div>

        {{-- TABLA OFICIAL --}}
        <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-sm">
            <table class="min-w-[1100px] w-full border-collapse text-sm">
                <thead>
                    <tr class="text-center text-xs font-bold uppercase text-white">
                        <th
                            rowspan="2"
                            class="border border-slate-300 bg-slate-700 px-4 py-4 text-left"
                        >
                            Curso
                        </th>

                        <th
                            rowspan="2"
                            class="border border-slate-300 bg-slate-700 px-3 py-4"
                        >
                            # Grupos
                        </th>

                        <th
                            colspan="3"
                            class="border border-blue-300 bg-blue-700 px-3 py-3"
                        >
                            Iniciaron
                        </th>

                        <th
                            colspan="3"
                            class="border border-orange-300 bg-orange-600 px-3 py-3"
                        >
                            Desertados
                        </th>

                        <th
                            colspan="3"
                            class="border border-green-300 bg-green-700 px-3 py-3"
                        >
                            Graduados
                        </th>
                    </tr>

                    <tr class="text-center text-xs font-semibold text-white">
                        @foreach (['M', 'F', 'Total'] as $sub)
                            <th class="border border-blue-300 bg-blue-600 px-3 py-3">
                                {{ $sub }}
                            </th>
                        @endforeach

                        @foreach (['M', 'F', 'Total'] as $sub)
                            <th class="border border-orange-300 bg-orange-500 px-3 py-3">
                                {{ $sub }}
                            </th>
                        @endforeach

                        @foreach (['M', 'F', 'Total'] as $sub)
                            <th class="border border-green-300 bg-green-600 px-3 py-3">
                                {{ $sub }}
                            </th>
                        @endforeach
                    </tr>
                </thead>

                <tbody>
                    @forelse ($filas as $fila)
                        <tr class="hover:bg-slate-50">
                            <td class="border border-slate-200 px-4 py-3 font-semibold text-slate-900">
                                {{ $fila->curso }}
                            </td>

                            <td class="border border-slate-200 px-3 py-3 text-center">
                                {{ $fila->numero_de_cursos }}
                            </td>

                            @foreach ([
                                'iniciaron_masculino',
                                'iniciaron_femenino',
                                'iniciaron_total',
                            ] as $campo)
                                <td class="border border-blue-100 bg-blue-50/40 px-3 py-3 text-center {{ $campo === 'iniciaron_total' ? 'font-bold' : '' }}">
                                    {{ $fila->$campo }}
                                </td>
                            @endforeach

                            @foreach ([
                                'desertados_masculino',
                                'desertados_femenino',
                                'desertados_total',
                            ] as $campo)
                                <td class="border border-orange-100 bg-orange-50/40 px-3 py-3 text-center {{ $campo === 'desertados_total' ? 'font-bold' : '' }}">
                                    {{ $fila->$campo }}
                                </td>
                            @endforeach

                            @foreach ([
                                'graduados_masculino',
                                'graduados_femenino',
                                'graduados_total',
                            ] as $campo)
                                <td class="border border-green-100 bg-green-50/40 px-3 py-3 text-center {{ $campo === 'graduados_total' ? 'font-bold' : '' }}">
                                    {{ $fila->$campo }}
                                </td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td
                                colspan="11"
                                class="px-4 py-10 text-center text-slate-500"
                            >
                                No se encontraron grupos finalizados
                                para el período seleccionado.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

                <tfoot>
                    <tr class="bg-blue-100 font-bold text-slate-900">
                        <td class="border border-blue-200 px-4 py-4">
                            TOTAL GENERAL
                        </td>

                        @foreach ($campos as $campo)
                            <td class="border border-blue-200 px-3 py-4 text-center">
                                {{ $totales->$campo }}
                            </td>
                        @endforeach
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- AVISO DE REVISION --}}
        @if ($advertencias->isNotEmpty())
            <div class="rounded-xl border border-amber-300 bg-amber-50 p-4">
                <p class="font-bold text-amber-900">
                    El reporte contiene resultados que requieren revisión.
                </p>

                <p class="mt-1 text-sm text-amber-800">
                    Se encontraron {{ $advertencias->count() }}
                    observaciones en grupos finalizados.
                    Verifica los registros antes de utilizar
                    las cifras como informe oficial.
                </p>
            </div>
        @endif

        {{-- REVISION ADMINISTRATIVA --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="mb-5">
                <h2 class="text-lg font-bold text-slate-900">
                    Revisión administrativa
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Esta información es interna y no aparece en el PDF oficial.
                </p>
            </div>

            <div class="mb-6 grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-xl bg-amber-50 p-4">
                    <p class="text-xs font-semibold text-amber-800">
                        En curso
                    </p>
                    <p class="mt-1 text-2xl font-bold text-amber-900">
                        {{ $enCurso->count() }}
                    </p>
                </div>

                <div class="rounded-xl bg-sky-50 p-4">
                    <p class="text-xs font-semibold text-sky-800">
                        Planificados
                    </p>
                    <p class="mt-1 text-2xl font-bold text-sky-900">
                        {{ $planificados->count() }}
                    </p>
                </div>

                <div class="rounded-xl bg-red-50 p-4">
                    <p class="text-xs font-semibold text-red-800">
                        Cancelados
                    </p>
                    <p class="mt-1 text-2xl font-bold text-red-900">
                        {{ $cancelados->count() }}
                    </p>
                </div>

                <div class="rounded-xl bg-orange-50 p-4">
                    <p class="text-xs font-semibold text-orange-800">
                        Resultados pendientes
                    </p>
                    <p class="mt-1 text-2xl font-bold text-orange-900">
                        {{ $pendientes->count() }}
                    </p>
                </div>
            </div>

            {{-- GRUPOS EXCLUIDOS --}}
            <h3 class="mb-3 font-bold text-slate-800">
                Grupos excluidos del TOTALON
            </h3>

            @if ($gruposExcluidos->isEmpty())
                <p class="mb-6 rounded-lg bg-green-50 p-4 text-sm text-green-800">
                    No hay grupos excluidos con actividad en el período consultado.
                </p>
            @else
                <div class="mb-7 overflow-x-auto rounded-xl border border-slate-200">
                    <table class="min-w-[850px] w-full text-sm">
                        <thead class="bg-slate-100 text-left text-xs uppercase text-slate-600">
                            <tr>
                                <th class="px-4 py-3">Curso</th>
                                <th class="px-4 py-3">Grupo</th>
                                <th class="px-4 py-3">Estado</th>
                                <th class="px-4 py-3">Inicio</th>
                                <th class="px-4 py-3">Fin</th>
                                <th class="px-4 py-3">Motivo</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100">
                            @foreach ($gruposExcluidos as $grupo)
                                <tr>
                                    <td class="px-4 py-3">
                                        {{ $grupo->curso }}
                                    </td>

                                    <td class="px-4 py-3 font-semibold">
                                        <a
                                            href="{{ route('grupos.show', $grupo->id) }}"
                                            class="text-blue-700 hover:underline"
                                        >
                                            {{ $grupo->codigo_grupo }}
                                        </a>
                                    </td>

                                    <td class="px-4 py-3">
                                        <span class="rounded-full bg-slate-100 px-2 py-1 text-xs font-semibold text-slate-700">
                                            {{ $grupo->estado }}
                                        </span>
                                    </td>

                                    <td class="px-4 py-3">
                                        {{ \Carbon\Carbon::parse($grupo->fecha_inicio)->format('d/m/Y') }}
                                    </td>

                                    <td class="px-4 py-3">
                                        {{ \Carbon\Carbon::parse($grupo->fecha_fin)->format('d/m/Y') }}
                                    </td>

                                    <td class="px-4 py-3 text-slate-600">
                                        {{ $grupo->motivo }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            {{-- RESULTADOS PENDIENTES --}}
            <h3 class="mb-3 font-bold text-slate-800">
                Resultados académicos pendientes
            </h3>

            @if ($pendientes->isEmpty())
                <p class="mb-6 rounded-lg bg-green-50 p-4 text-sm text-green-800">
                    No se encontraron resultados pendientes
                    en los grupos finalizados incluidos.
                </p>
            @else
                <div class="mb-7 space-y-3">
                    @foreach ($advertenciasPorGrupo as $grupoId => $observaciones)
                        @php
                            $pendientesGrupo = $observaciones
                                ->where('tipo', 'resultado_pendiente');
                        @endphp

                        @if ($pendientesGrupo->isNotEmpty())
                            <details class="rounded-xl border border-amber-200 bg-amber-50">
                                <summary class="cursor-pointer px-4 py-3 font-semibold text-amber-900">
                                    {{ $observaciones->first()['curso'] }}
                                    —
                                    {{ $observaciones->first()['grupo'] }}

                                    ({{ $pendientesGrupo->count() }} pendientes)
                                </summary>

                                <div class="border-t border-amber-200 bg-white p-4">
                                    <p class="mb-3 text-sm text-slate-600">
                                        Estas inscripciones pertenecen a un
                                        grupo finalizado, pero todavía tienen
                                        resultado académico pendiente.
                                    </p>

                                    <div class="space-y-2">
                                        @foreach ($pendientesGrupo as $aviso)
                                            <div class="flex flex-wrap items-center justify-between gap-2 rounded-lg border border-slate-200 p-3">
                                                <div>
                                                    <p class="font-semibold text-slate-800">
                                                        Estudiante #{{ $aviso['estudiante_id'] }}
                                                    </p>

                                                    <p class="text-xs text-slate-500">
                                                        Inscripción #{{ $aviso['inscripcion_id'] }}
                                                        —
                                                        {{ $aviso['mensaje'] }}
                                                    </p>
                                                </div>

                                                <a
                                                    href="{{ route('grupos.evaluaciones.editar', [
                                                        'grupo' => $aviso['grupo_id'],
                                                        'inscripcion' => $aviso['inscripcion_id'],
                                                    ]) }}"
                                                    class="rounded-lg bg-amber-600 px-3 py-2 text-xs font-semibold text-white hover:bg-amber-700"
                                                >
                                                    Revisar evaluaciones
                                                </a>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </details>
                        @endif
                    @endforeach
                </div>
            @endif

            {{-- OTRAS INCONSISTENCIAS --}}
            <h3 class="mb-3 font-bold text-slate-800">
                Otras observaciones
            </h3>

            @if ($errores->isEmpty())
                <p class="rounded-lg bg-green-50 p-4 text-sm text-green-800">
                    No se detectaron otras inconsistencias
                    mediante las validaciones actuales.
                </p>
            @else
                <div class="space-y-2">
                    @foreach ($errores as $aviso)
                        <div class="rounded-xl border border-red-200 bg-red-50 p-4">
                            <p class="font-semibold text-red-900">
                                {{ $aviso['curso'] }}
                                —
                                {{ $aviso['grupo'] }}
                            </p>

                            <p class="mt-1 text-sm text-red-800">
                                Estudiante #{{ $aviso['estudiante_id'] }}:
                                {{ $aviso['mensaje'] }}
                            </p>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <p class="text-xs text-slate-500">
            Metodología: únicamente se contabilizan los grupos con
            estado FINALIZADO cuya fecha de fin pertenece al rango
            seleccionado. Cada inscripción cuenta como una participación.
            Los graduados se obtienen del resultado académico GRADUADO.
        </p>
    </div>
</x-app-layout>
