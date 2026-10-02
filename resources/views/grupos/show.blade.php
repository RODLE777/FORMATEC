@php
    $tabInicial = request('tab', 'informacion');
@endphp

<x-app-layout :title="$grupo->codigo_grupo">
    <div x-data="{ tab: '{{ $tabInicial }}' }">

        <div class="mb-1 flex items-start justify-between">
            <div>
                <h2 class="text-xl font-bold text-slate-900">{{ $grupo->curso->nombre }} — {{ $grupo->codigo_grupo }}</h2>
                <p class="text-sm text-slate-500">
                    {{ $grupo->profesor?->nombre_completo ?? 'Sin profesor asignado' }} ·
                    {{ $grupo->mes }}/{{ $grupo->anio }} ·
                    <span class="rounded-full px-2 py-0.5 text-xs
                        {{ $grupo->estado === 'EN_CURSO' ? 'bg-blue-50 text-blue-700' : '' }}
                        {{ $grupo->estado === 'FINALIZADO' ? 'bg-emerald-50 text-emerald-700' : '' }}
                        {{ $grupo->estado === 'CANCELADO' ? 'bg-red-50 text-red-700' : '' }}
                        {{ $grupo->estado === 'PLANIFICADO' ? 'bg-slate-100 text-slate-600' : '' }}">
                        {{ $grupo->estado }}
                    </span>
                </p>
            </div>
            @can('update', $grupo)
                <a href="{{ route('grupos.edit', $grupo) }}" class="rounded-lg bg-slate-100 px-4 py-2 text-sm font-semibold hover:bg-slate-200">
                    Editar informacion
                </a>
            @endcan
        </div>

        
        <div class="mt-6 border-b border-slate-200">
            <nav class="-mb-px flex gap-6 text-sm">
               @foreach ([
    'informacion'         => 'Informacion',
    'estudiantes'         => 'Estudiantes ('.$grupo->inscripciones->count().')',
    'sesiones'            => 'Sesiones ('.$grupo->sesiones->count().')',
    'asistencia'          => 'Asistencia',
    'config-evaluaciones' => 'Config. evaluaciones',
    'reportes'            => 'Reportes',
] as $key => $label)
                    <button @click="tab = '{{ $key }}'"
                            :class="tab === '{{ $key }}' ? 'border-slate-900 text-slate-900 font-semibold' : 'border-transparent text-slate-500 hover:text-slate-700'"
                            class="border-b-2 pb-3 pt-1">
                        {{ $label }}
                    </button>
                @endforeach
            </nav>
        </div>

        {{-- Informacion --}}
        <div x-show="tab === 'informacion'" class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div class="rounded-xl bg-white p-5 border border-slate-100 shadow-sm text-sm space-y-2">
                <p><span class="text-slate-500">Fecha inicio:</span> {{ $grupo->fecha_inicio?->format('d/m/Y') ?? '—' }}</p>
                <p><span class="text-slate-500">Fecha fin:</span> {{ $grupo->fecha_fin?->format('d/m/Y') ?? '—' }}</p>
                <p><span class="text-slate-500">Duracion:</span> {{ $grupo->duracion_horas ?? '—' }} horas</p>
                <p><span class="text-slate-500">Horario:</span> {{ $grupo->horario ?? '—' }}</p>
                <p><span class="text-slate-500">Lugar:</span> {{ $grupo->lugar ?? '—' }}</p>
                <p><span class="text-slate-500">Evaluaciones:</span> {{ $grupo->numero_evaluaciones }}</p>
                <p><span class="text-slate-500">Certificacion:</span> {{ $grupo->plataforma_certificacion ?? '—' }}</p>
            </div>
        </div>

        {{-- Estudiantes: inscribir, editar estado, quitar y acceder a evaluaciones desde aqui --}}
        <div x-show="tab === 'estudiantes'" x-data="{ mostrarTodos: false }" class="mt-6">
            @can('update', $grupo)
                <a href="{{ route('inscripciones.create', ['grupo_id' => $grupo->id]) }}"
                   class="mb-3 inline-block rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                    + Inscribir estudiante
                </a>
                @if ($grupo->cupo_maximo)
                    <span class="ml-2 text-sm text-slate-500">
                        Cupo: {{ $grupo->inscripciones->where('estado', 'ACTIVA')->count() }}/{{ $grupo->cupo_maximo }}
                    </span>
                @endif
            @endcan

            {{-- overflow-visible (no overflow-hidden) a proposito: el
            popover de "Editar estado" se sale del borde de la tabla. --}}
            <div class="overflow-visible rounded-xl border border-slate-200 bg-white">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-xs uppercase text-slate-500">
                        <tr>
                            <th class="px-4 py-3">Estudiante</th>
                            <th class="px-4 py-3">Estado</th>
                            <th class="px-4 py-3">Resultado</th>
                            <th class="px-4 py-3">Nota final</th>
                            <th class="px-4 py-3 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($grupo->inscripciones as $index => $inscripcion)
                            <tr x-show="mostrarTodos || {{ $index }} < 20">
                                <td class="px-4 py-3 font-medium text-slate-900">
                                    <a href="{{ route('estudiantes.show', $inscripcion->estudiante) }}" class="hover:underline">
                                        {{ $inscripcion->estudiante->nombre_completo }}
                                    </a>
                                </td>
                                <td class="px-4 py-3 text-slate-600">{{ $inscripcion->estado }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ $inscripcion->resultado_final }}</td>
                                {{-- nota_final: solo lectura, la calcula el trigger --}}
                                <td class="px-4 py-3 font-mono text-slate-700">{{ $inscripcion->nota_final ?? '—' }}</td>
                                <td class="px-4 py-3 text-right space-x-3 relative" x-data="{ editando: false }">
                                    @can('update', $grupo)
                                        <a href="{{ route('grupos.evaluaciones.editar', [$grupo, $inscripcion]) }}" class="text-slate-600 hover:underline">
                                            Registrar notas
                                        </a>
                                        <button type="button" @click="editando = !editando" class="text-slate-600 hover:underline">
                                            Editar estado
                                        </button>
                                    @endcan
                                    @if ($inscripcion->resultado_final === 'GRADUADO')
                                        <a href="{{ route('grupos.reportes.constancia-estudiante', [$grupo, $inscripcion]) }}" class="text-slate-600 hover:underline">
                                            Constancia
                                        </a>
                                    @endif
                                    @can('update', $grupo)
                                        <form method="POST" action="{{ route('inscripciones.destroy', $inscripcion) }}" class="inline"
                                              onsubmit="return confirm('¿Quitar a este estudiante del grupo? Solo funciona si aun no tiene notas ni asistencia registrada.')">
                                            @csrf @method('DELETE')
                                            <button class="text-red-600 hover:underline">Quitar</button>
                                        </form>
                                    @endcan

                                    {{-- Popover: corrige a mano el estado/resultado cuando el
                                    trigger decidio mal o hay que revertir una deserción
                                    automatica. Si se vuelve a poner EN_CURSO, el trigger
                                    puede volver a decidir solo la proxima vez que se
                                    guarden notas. --}}
                                    <div x-show="editando" x-cloak @click.outside="editando = false"
                                         class="absolute right-0 z-20 mt-2 w-72 rounded-xl border border-slate-200 bg-white p-4 shadow-lg text-left">
                                        <form method="POST" action="{{ route('inscripciones.estado', $inscripcion) }}" class="space-y-2">
                                            @csrf @method('PUT')
                                            <div>
                                                <label class="block text-xs font-medium text-slate-500">Estado</label>
                                                <select name="estado" class="mt-1 w-full rounded-lg border border-slate-300 px-2 py-1.5 text-sm">
                                                    @foreach (['ACTIVA','RETIRADA','FINALIZADA'] as $estado)
                                                        <option value="{{ $estado }}" {{ $inscripcion->estado === $estado ? 'selected' : '' }}>{{ $estado }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-slate-500">Resultado</label>
                                                <select name="resultado_final" class="mt-1 w-full rounded-lg border border-slate-300 px-2 py-1.5 text-sm">
                                                    @foreach (['EN_CURSO','GRADUADO','DESERTADO','REPROBADO'] as $resultado)
                                                        <option value="{{ $resultado }}" {{ $inscripcion->resultado_final === $resultado ? 'selected' : '' }}>{{ $resultado }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-slate-500">Observaciones (opcional)</label>
                                                <input type="text" name="observaciones" value="{{ $inscripcion->observaciones }}"
                                                       class="mt-1 w-full rounded-lg border border-slate-300 px-2 py-1.5 text-sm">
                                            </div>
                                            <p class="text-[11px] text-slate-400">
                                                Si lo vuelves a poner en "EN_CURSO", el sistema podra decidir el resultado solo otra vez cuando termines de registrar sus notas.
                                            </p>
                                            <button class="w-full rounded-lg bg-slate-900 px-3 py-1.5 text-sm font-semibold text-white hover:bg-slate-800">
                                                Guardar
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-4 py-6 text-center text-slate-400">Aun no hay estudiantes inscritos.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($grupo->inscripciones->count() > 20)
                <button @click="mostrarTodos = !mostrarTodos" class="mt-2 text-sm text-slate-500 hover:underline">
                    <span x-text="mostrarTodos ? 'Mostrar menos' : 'Mostrar los {{ $grupo->inscripciones->count() }} estudiantes'"></span>
                </button>
            @endif
        </div>

        {{-- Sesiones --}}
        <div x-show="tab === 'sesiones'" x-data="{ mostrarTodas: false }" class="mt-6">
            @can('update', $grupo)
                <form method="POST" action="{{ route('grupos.sesiones.store', $grupo) }}" class="mb-4 flex flex-wrap items-end gap-2 rounded-xl border border-slate-100 bg-white p-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-medium text-slate-500"># Sesion</label>
                        <input type="number" name="numero_sesion" required min="1" class="mt-1 w-24 rounded-lg border border-slate-300 px-2 py-1.5 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-500">Fecha</label>
                        <input type="date" name="fecha" required class="mt-1 rounded-lg border border-slate-300 px-2 py-1.5 text-sm">
                    </div>
                    <div class="flex-1 min-w-[10rem]">
                        <label class="block text-xs font-medium text-slate-500">Tema</label>
                        <input type="text" name="tema" class="mt-1 w-full rounded-lg border border-slate-300 px-2 py-1.5 text-sm">
                    </div>
                    <button class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                        + Agregar sesion
                    </button>
                </form>
            @endcan

            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-xs uppercase text-slate-500">
                        <tr>
                            <th class="px-4 py-3">#</th>
                            <th class="px-4 py-3">Fecha</th>
                            <th class="px-4 py-3">Tema</th>
                            <th class="px-4 py-3 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($grupo->sesiones as $index => $sesion)
                            <tr x-show="mostrarTodas || {{ $index }} < 20">
                                <td class="px-4 py-3">{{ $sesion->numero_sesion }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ $sesion->fecha->format('d/m/Y') }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ $sesion->tema ?? '—' }}</td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('grupos.sesiones.pasar-lista', [$grupo, $sesion]) }}" class="text-slate-600 hover:underline">
                                        Pasar lista
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-4 py-6 text-center text-slate-400">Aun no hay sesiones registradas.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($grupo->sesiones->count() > 20)
                <button @click="mostrarTodas = !mostrarTodas" class="mt-2 text-sm text-slate-500 hover:underline">
                    <span x-text="mostrarTodas ? 'Mostrar menos' : 'Mostrar las {{ $grupo->sesiones->count() }} sesiones'"></span>
                </button>
            @endif
        </div>

        {{-- Asistencia: resumen consolidado; el registro real ocurre en "pasar lista" por sesion --}}
        <div x-show="tab === 'asistencia'" class="mt-6">
            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-xs uppercase text-slate-500">
                        <tr>
                            <th class="px-4 py-3">Estudiante</th>
                            @foreach ($grupo->sesiones as $sesion)
                                <th class="px-3 py-3 text-center">S{{ $sesion->numero_sesion }}</th>
                            @endforeach
                            <th class="px-4 py-3 text-center">% Asistencia</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($grupo->inscripciones as $inscripcion)
                            <tr>
                                <td class="px-4 py-3 font-medium text-slate-900">{{ $inscripcion->estudiante->nombre_completo }}</td>
                                @foreach ($grupo->sesiones as $sesion)
                                    @php
                                        $asistio = $inscripcion->asistencias->firstWhere('sesion_id', $sesion->id)?->asistio;
                                    @endphp
                                    <td class="px-3 py-3 text-center">
                                        @if ($asistio === null) <span class="text-slate-300">·</span>
                                        @elseif ($asistio) <span class="text-emerald-600">✓</span>
                                        @else <span class="text-red-500">✕</span>
                                        @endif
                                    </td>
                                @endforeach
                                <td class="px-4 py-3 text-center font-mono">{{ $inscripcion->porcentajeAsistencia() ?? '—' }}%</td>
                            </tr>
                        @empty
                            <tr><td colspan="99" class="px-4 py-6 text-center text-slate-400">Sin datos aun.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <p class="mt-2 text-xs text-slate-400">Para registrar asistencia de una sesion especifica, entra a la pestaña Sesiones y usa "Pasar lista".</p>
        </div>
{{-- Config. evaluaciones: solo ROOT/ADMIN editan, todos ven --}}
<div x-show="tab === 'config-evaluaciones'" class="mt-6">
    <div class="rounded-xl border border-slate-200 bg-white p-5">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="font-semibold text-slate-900">Configuración de evaluaciones</h3>
                <p class="text-sm text-slate-500">
                    Porcentajes globales del grupo. Las notas sin registrar cuentan como 0.
                </p>
            </div>
            @if (in_array(auth()->user()->rol, ['ROOT', 'ADMINISTRADOR']))
                <a href="{{ route('grupos.config-evaluaciones', $grupo) }}"
                   class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                    Editar configuración
                </a>
            @endif
        </div>

        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-left text-xs uppercase text-slate-500">
                <tr>
                    <th class="px-4 py-2">#</th>
                    <th class="px-4 py-2">Nombre</th>
                    <th class="px-4 py-2 text-right">Porcentaje</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @php $configActual = $grupo->configEvaluaciones(); $suma = 0; @endphp
                @foreach ($configActual as $i => $ev)
                    @php $suma += $ev['porcentaje']; @endphp
                    <tr>
                        <td class="px-4 py-2 text-slate-500">{{ $i + 1 }}</td>
                        <td class="px-4 py-2 text-slate-800">{{ $ev['nombre'] }}</td>
                        <td class="px-4 py-2 text-right font-mono">{{ number_format($ev['porcentaje'], 2) }}%</td>
                    </tr>
                @endforeach
                <tr class="bg-slate-50 font-semibold">
                    <td colspan="2" class="px-4 py-2 text-right">Suma:</td>
                    <td class="px-4 py-2 text-right font-mono
                        {{ abs($suma - 100) < 0.01 ? 'text-emerald-700' : 'text-red-700' }}">
                        {{ number_format($suma, 2) }}%
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
        {{-- Reportes: nacen desde el grupo, no desde un modulo aislado --}}
        <div x-show="tab === 'reportes'" class="mt-6 grid gap-4 sm:grid-cols-2">
            <a href="{{ route('grupos.reportes.notas', $grupo) }}"
               class="rounded-xl bg-white p-5 border border-slate-100 shadow-sm hover:border-slate-300">
                <p class="font-semibold text-slate-900">Acta de notas (PDF)</p>
                <p class="text-sm text-slate-500 mt-1">PDF con las evaluaciones y nota final de cada estudiante del grupo.</p>
            </a>
            <a href="{{ route('grupos.reportes.notas-excel', $grupo) }}"
               class="rounded-xl bg-white p-5 border border-slate-100 shadow-sm hover:border-slate-300">
                <p class="font-semibold text-slate-900">Acta de notas (Excel)</p>
                <p class="text-sm text-slate-500 mt-1">Mismo detalle en .xlsx, editable.</p>
            </a>
            <a href="{{ route('grupos.reportes.asistencia', $grupo) }}"
               class="rounded-xl bg-white p-5 border border-slate-100 shadow-sm hover:border-slate-300">
                <p class="font-semibold text-slate-900">Constancia de asistencia</p>
                <p class="text-sm text-slate-500 mt-1">PDF con el detalle de asistencia por sesion de todo el grupo.</p>
            </a>
            <div class="rounded-xl bg-slate-50 p-5 border border-slate-100 sm:col-span-2">
                <p class="text-sm text-slate-500">
                    Para la constancia individual de un estudiante graduado, entra a la pestaña
                    "Estudiantes" y usa "Constancia" junto a su nombre.
                </p>
            </div>
        </div>
    </div>
</x-app-layout>
