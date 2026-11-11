@php
    $tabInicial = request('tab', 'informacion');

    $tabsPermitidas = [
        'informacion',
        'estudiantes',
        'sesiones',
        'asistencia',
        'config-evaluaciones',
        'reportes',
    ];

    if (!in_array($tabInicial, $tabsPermitidas, true)) {
        $tabInicial = 'informacion';
    }

    $usuario = auth()->user();

    $esAdmin = in_array(
        $usuario->rol,
        ['ROOT', 'ADMINISTRADOR'],
        true
    );

    $esProfesorAsignado = $usuario->esProfesor()
        && $usuario->profesor
        && (int) $grupo->profesor_id === (int) $usuario->profesor->id;

    $puedeRegistrar = $esAdmin || (
        $esProfesorAsignado && $grupo->estado === 'EN_CURSO'
    );

    $puedeGestionarClase = $puedeRegistrar;

    $totalInscritos = $grupo->inscripciones->count();

    $inscritosActivos = $grupo->inscripciones
        ->where('estado', 'ACTIVA')
        ->count();
@endphp

<x-app-layout :title="$grupo->codigo_grupo">
    <div x-data="{ tab: @js($tabInicial) }">

        {{-- ENCABEZADO --}}
        <div class="mb-6 flex flex-wrap items-start justify-between gap-4">
            <div>
                <a href="{{ route('grupos.index') }}"
                   class="mb-2 inline-block text-sm text-slate-500 hover:text-emerald-700">
                    ← Volver a mis grupos
                </a>

                <h2 class="text-xl font-bold text-slate-900">
                    {{ $grupo->curso?->nombre ?? 'Curso' }}
                    — {{ $grupo->codigo_grupo }}
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    {{ $grupo->profesor?->nombre_completo ?? 'Sin profesor' }}
                    · {{ $grupo->mes }}/{{ $grupo->anio }}
                </p>

                <div class="mt-3 flex flex-wrap gap-2">
                    <span @class([
                        'rounded-full px-3 py-1 text-xs font-semibold',
                        'bg-blue-50 text-blue-700' => $grupo->estado === 'EN_CURSO',
                        'bg-emerald-50 text-emerald-700' => $grupo->estado === 'FINALIZADO',
                        'bg-red-50 text-red-700' => $grupo->estado === 'CANCELADO',
                        'bg-slate-100 text-slate-600' => $grupo->estado === 'PLANIFICADO',
                    ])>
                        {{ str_replace('_', ' ', $grupo->estado) }}
                    </span>

                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs text-slate-600">
                        {{ $totalInscritos }} estudiantes
                    </span>
                </div>
            </div>

            @if($esAdmin)
                <a href="{{ route('grupos.edit', $grupo) }}"
                   class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                    Editar información
                </a>
            @endif
        </div>

        @if(session('status'))
            <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800">
                {{ session('status') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-4 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800">
                <ul class="list-disc pl-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(!$esAdmin && $grupo->estado !== 'EN_CURSO')
            <div class="mb-5 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
                Este grupo está {{ strtolower(str_replace('_', ' ', $grupo->estado)) }}.
                Puedes consultar la información, pero no registrar nuevas notas o asistencias.
            </div>
        @endif

        {{-- PESTAÑAS --}}
        <div class="overflow-x-auto border-b border-slate-200">
            <nav class="flex min-w-max gap-6 text-sm">
                @foreach([
                    'informacion' => 'Información',
                    'estudiantes' => 'Estudiantes ('.$totalInscritos.')',
                    'sesiones' => 'Sesiones ('.$grupo->sesiones->count().')',
                    'asistencia' => 'Asistencia',
                    'config-evaluaciones' => 'Evaluaciones',
                    'reportes' => 'Reportes',
                ] as $key => $label)
                    <button type="button"
                            @click="tab = '{{ $key }}'"
                            :class="tab === '{{ $key }}'
                                ? 'border-emerald-600 text-emerald-700 font-semibold'
                                : 'border-transparent text-slate-500 hover:text-slate-800'"
                            class="whitespace-nowrap border-b-2 px-1 pb-3 pt-2">
                        {{ $label }}
                    </button>
                @endforeach
            </nav>
        </div>

        {{-- INFORMACIÓN --}}
        <div x-show="tab === 'informacion'"
             class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">

            <div class="rounded-xl border border-slate-200 bg-white p-5">
                <h3 class="mb-4 font-semibold text-slate-900">
                    Información del grupo
                </h3>

                <div class="space-y-3 text-sm">
                    <p>
                        <span class="text-slate-500">Inicio:</span>
                        {{ $grupo->fecha_inicio?->format('d/m/Y') ?? '—' }}
                    </p>

                    <p>
                        <span class="text-slate-500">Finalización:</span>
                        {{ $grupo->fecha_fin?->format('d/m/Y') ?? '—' }}
                    </p>

                    <p>
                        <span class="text-slate-500">Duración:</span>
                        {{ $grupo->duracion_horas ?? '—' }} horas
                    </p>

                    <p>
                        <span class="text-slate-500">Horario:</span>
                        {{ $grupo->horario ?? '—' }}
                    </p>

                    <p>
                        <span class="text-slate-500">Lugar:</span>
                        {{ $grupo->lugar ?? '—' }}
                    </p>

                    <p>
                        <span class="text-slate-500">Evaluaciones:</span>
                        {{ $grupo->numero_evaluaciones }}
                    </p>

                    <p>
                        <span class="text-slate-500">Certificación:</span>
                        {{ $grupo->plataforma_certificacion ?? '—' }}
                    </p>
                </div>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-5">
                <h3 class="mb-4 font-semibold text-slate-900">
                    Resumen académico
                </h3>

                <div class="space-y-3 text-sm">
                    <p class="flex justify-between">
                        <span class="text-slate-500">Inscripciones</span>
                        <strong>{{ $totalInscritos }}</strong>
                    </p>

                    <p class="flex justify-between">
                        <span class="text-slate-500">Activas</span>
                        <strong>{{ $inscritosActivos }}</strong>
                    </p>

                    <p class="flex justify-between">
                        <span class="text-slate-500">Sesiones</span>
                        <strong>{{ $grupo->sesiones->count() }}</strong>
                    </p>

                    <p class="flex justify-between">
                        <span class="text-slate-500">Cupo máximo</span>
                        <strong>{{ $grupo->cupo_maximo ?? 'Sin límite' }}</strong>
                    </p>
                </div>
            </div>
        </div>

        {{-- ESTUDIANTES --}}
        <div x-show="tab === 'estudiantes'"
             x-data="{ mostrarTodos: false }"
             class="mt-6">

            <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h3 class="font-semibold text-slate-900">
                        Estudiantes inscritos
                    </h3>

                    <p class="text-sm text-slate-500">
                        {{ $totalInscritos }} registros en este grupo
                    </p>
                </div>

                @if($esAdmin)
                    <a href="{{ route('inscripciones.create', ['grupo_id' => $grupo->id]) }}"
                       class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                        + Inscribir estudiante
                    </a>
                @endif
            </div>

            <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white">
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
                        @forelse($grupo->inscripciones as $index => $inscripcion)
                            <tr x-show="mostrarTodos || {{ $index }} < 20">
                                <td class="px-4 py-3">
                                    <a href="{{ route('estudiantes.show', $inscripcion->estudiante) }}"
                                       class="font-semibold text-slate-900 hover:text-emerald-700">
                                        {{ $inscripcion->estudiante->nombre_completo }}
                                    </a>
                                </td>

                                <td class="px-4 py-3 text-slate-600">
                                    {{ $inscripcion->estado }}
                                </td>

                                <td class="px-4 py-3 text-slate-600">
                                    {{ $inscripcion->resultado_final }}
                                </td>

                                <td class="px-4 py-3 font-mono text-slate-700">
                                    {{ $inscripcion->nota_final ?? '—' }}
                                </td>

                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap justify-end gap-3">
                                        @if($puedeRegistrar)
                                            <a href="{{ route('grupos.evaluaciones.editar', [$grupo, $inscripcion]) }}"
                                               class="text-emerald-700 hover:underline">
                                                Registrar notas
                                            </a>
                                        @endif

                                        @if($inscripcion->resultado_final === 'GRADUADO')
                                            <a href="{{ route('grupos.reportes.constancia-estudiante', [$grupo, $inscripcion]) }}"
                                               class="text-slate-600 hover:underline">
                                                Constancia
                                            </a>
                                        @endif

                                        @if($esAdmin)
                                            <div x-data="{ editando: false }" class="relative">
                                                <button type="button"
                                                        @click="editando = !editando"
                                                        class="text-slate-600 hover:underline">
                                                    Editar estado
                                                </button>

                                                <div x-show="editando"
                                                     x-cloak
                                                     @click.outside="editando = false"
                                                     class="absolute right-0 z-30 mt-2 w-72 rounded-xl border border-slate-200 bg-white p-4 text-left shadow-xl">

                                                    <form method="POST"
                                                          action="{{ route('inscripciones.estado', $inscripcion) }}"
                                                          class="space-y-3">
                                                        @csrf
                                                        @method('PUT')

                                                        <div>
                                                            <label class="block text-xs text-slate-500">
                                                                Estado
                                                            </label>

                                                            <select name="estado"
                                                                    class="mt-1 w-full rounded-lg border border-slate-300 text-sm">
                                                                @foreach(['ACTIVA','RETIRADA','FINALIZADA'] as $estado)
                                                                    <option value="{{ $estado }}"
                                                                        @selected($inscripcion->estado === $estado)>
                                                                        {{ $estado }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>

                                                        <div>
                                                            <label class="block text-xs text-slate-500">
                                                                Resultado
                                                            </label>

                                                            <select name="resultado_final"
                                                                    class="mt-1 w-full rounded-lg border border-slate-300 text-sm">
                                                                @foreach(['EN_CURSO','GRADUADO','DESERTADO','REPROBADO'] as $resultado)
                                                                    <option value="{{ $resultado }}"
                                                                        @selected($inscripcion->resultado_final === $resultado)>
                                                                        {{ $resultado }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>

                                                        <div>
                                                            <label class="block text-xs text-slate-500">
                                                                Observaciones
                                                            </label>

                                                            <input type="text"
                                                                   name="observaciones"
                                                                   value="{{ $inscripcion->observaciones }}"
                                                                   class="mt-1 w-full rounded-lg border border-slate-300 text-sm">
                                                        </div>

                                                        <button type="submit"
                                                                class="w-full rounded-lg bg-slate-900 px-3 py-2 text-sm font-semibold text-white">
                                                            Guardar cambios
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>

                                            <form method="POST"
                                                  action="{{ route('inscripciones.destroy', $inscripcion) }}"
                                                  onsubmit="return confirm('¿Quitar a este estudiante del grupo?')">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit"
                                                        class="text-red-600 hover:underline">
                                                    Quitar
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-slate-500">
                                    No hay estudiantes inscritos.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($totalInscritos > 20)
                <button type="button"
                        @click="mostrarTodos = !mostrarTodos"
                        class="mt-3 text-sm font-medium text-emerald-700 hover:underline">
                    <span x-text="mostrarTodos ? 'Mostrar menos' : 'Mostrar todos los estudiantes'"></span>
                </button>
            @endif
        </div>

        {{-- SESIONES --}}
        <div x-show="tab === 'sesiones'" class="mt-6">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="font-semibold text-slate-900">
                    Sesiones del grupo
                </h3>
            </div>

            @if($puedeGestionarClase)
                <form method="POST"
                      action="{{ route('grupos.sesiones.store', $grupo) }}"
                      class="mb-4 flex flex-wrap items-end gap-3 rounded-xl border border-slate-200 bg-white p-4">
                    @csrf

                    <div>
                        <label class="block text-xs text-slate-500">Número</label>

                        <input type="number"
                               name="numero_sesion"
                               min="1"
                               value="{{ old('numero_sesion', $grupo->sesiones->max('numero_sesion') + 1) }}"
                               required
                               class="mt-1 w-24 rounded-lg border border-slate-300 text-sm">
                    </div>

                    <div>
                        <label class="block text-xs text-slate-500">Fecha</label>

                        <input type="date"
                               name="fecha"
                               value="{{ old('fecha') }}"
                               required
                               class="mt-1 rounded-lg border border-slate-300 text-sm">
                    </div>

                    <div class="min-w-[180px] flex-1">
                        <label class="block text-xs text-slate-500">Tema</label>

                        <input type="text"
                               name="tema"
                               value="{{ old('tema') }}"
                               maxlength="200"
                               class="mt-1 w-full rounded-lg border border-slate-300 text-sm">
                    </div>

                    <button type="submit"
                            class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                        + Agregar sesión
                    </button>
                </form>
            @endif

            <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white">
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
                        @forelse($grupo->sesiones as $sesion)
                            @php
                                $tieneAsistencia = $sesion->asistencias()->exists();
                            @endphp

                            <tr>
                                <td class="px-4 py-3">
                                    {{ $sesion->numero_sesion }}
                                </td>

                                <td class="px-4 py-3 text-slate-600">
                                    {{ $sesion->fecha->format('d/m/Y') }}
                                </td>

                                <td class="px-4 py-3 text-slate-600">
                                    {{ $sesion->tema ?? '—' }}
                                </td>

                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap items-center justify-end gap-3">
                                        @if($puedeRegistrar)
                                            <a href="{{ route('grupos.sesiones.pasar-lista', [$grupo, $sesion]) }}"
                                               class="font-medium text-emerald-700 hover:underline">
                                                Pasar lista
                                            </a>
                                        @else
                                            <span class="text-xs text-slate-400">
                                                Solo lectura
                                            </span>
                                        @endif

                                        @if($puedeGestionarClase && !$tieneAsistencia)
                                            <form method="POST"
                                                  action="{{ route('grupos.sesiones.destroy', [$grupo, $sesion]) }}"
                                                  onsubmit="return confirm('¿Eliminar esta sesión? Esta acción no se puede deshacer.')">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit"
                                                        class="font-medium text-red-600 hover:underline">
                                                    Eliminar
                                                </button>
                                            </form>
                                        @endif

                                        @if($puedeGestionarClase && $tieneAsistencia)
                                            <span class="text-xs text-slate-400"
                                                  title="No se puede eliminar porque ya tiene asistencia registrada">
                                                Con asistencia
                                            </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4"
                                    class="px-4 py-8 text-center text-slate-500">
                                    No hay sesiones registradas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ASISTENCIA --}}
        <div x-show="tab === 'asistencia'" class="mt-6">
            <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-xs uppercase text-slate-500">
                        <tr>
                            <th class="px-4 py-3">Estudiante</th>

                            @foreach($grupo->sesiones as $sesion)
                                <th class="px-3 py-3 text-center">
                                    S{{ $sesion->numero_sesion }}
                                </th>
                            @endforeach

                            <th class="px-4 py-3 text-center">
                                % Asistencia
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @forelse($grupo->inscripciones as $inscripcion)
                            <tr>
                                <td class="px-4 py-3 font-medium text-slate-900">
                                    {{ $inscripcion->estudiante->nombre_completo }}
                                </td>

                                @foreach($grupo->sesiones as $sesion)
                                    @php
                                        $asistio = $inscripcion->asistencias
                                            ->firstWhere('sesion_id', $sesion->id)?->asistio;
                                    @endphp

                                    <td class="px-3 py-3 text-center">
                                        @if($asistio === null)
                                            <span class="text-slate-300">·</span>
                                        @elseif($asistio)
                                            <span class="text-emerald-600">✓</span>
                                        @else
                                            <span class="text-red-500">✕</span>
                                        @endif
                                    </td>
                                @endforeach

                                <td class="px-4 py-3 text-center font-mono">
                                    {{ $inscripcion->porcentajeAsistencia() ?? '—' }}%
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="99"
                                    class="px-4 py-8 text-center text-slate-500">
                                    Sin datos de asistencia.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <p class="mt-3 text-xs text-slate-500">
                Para registrar asistencia, entra a Sesiones y selecciona Pasar lista.
            </p>
        </div>

        {{-- CONFIGURACIÓN DE EVALUACIONES --}}
        <div x-show="tab === 'config-evaluaciones'" class="mt-6">
            <div class="rounded-xl border border-slate-200 bg-white p-5">
                <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h3 class="font-semibold text-slate-900">
                            Configuración de evaluaciones
                        </h3>

                        <p class="mt-1 text-sm text-slate-500">
                            Distribución de porcentajes del grupo.
                        </p>
                    </div>

                    @if($puedeGestionarClase)
                        <a href="{{ route('grupos.config-evaluaciones', $grupo) }}"
                           class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                            Editar configuración
                        </a>
                    @endif
                </div>

                @php
                    $configActual = $grupo->configEvaluaciones();
                    $suma = 0;
                @endphp

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-left text-xs uppercase text-slate-500">
                            <tr>
                                <th class="px-4 py-3">#</th>
                                <th class="px-4 py-3">Evaluación</th>
                                <th class="px-4 py-3 text-right">Porcentaje</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100">
                            @foreach($configActual as $i => $evaluacion)
                                @php
                                    $suma += (float) $evaluacion['porcentaje'];
                                @endphp

                                <tr>
                                    <td class="px-4 py-3">{{ $i + 1 }}</td>

                                    <td class="px-4 py-3">
                                        {{ $evaluacion['nombre'] }}
                                    </td>

                                    <td class="px-4 py-3 text-right font-mono">
                                        {{ number_format($evaluacion['porcentaje'], 2) }}%
                                    </td>
                                </tr>
                            @endforeach

                            <tr class="bg-slate-50 font-semibold">
                                <td colspan="2"
                                    class="px-4 py-3 text-right">
                                    Total
                                </td>

                                <td class="px-4 py-3 text-right">
                                    {{ number_format($suma, 2) }}%
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- REPORTES --}}
        <div x-show="tab === 'reportes'"
             class="mt-6 grid gap-4 sm:grid-cols-2">

            <a href="{{ route('grupos.reportes.notas', $grupo) }}"
               class="rounded-xl border border-slate-200 bg-white p-5 hover:border-emerald-400">
                <h3 class="font-semibold text-slate-900">
                    Acta de notas PDF
                </h3>

                <p class="mt-1 text-sm text-slate-500">
                    Resultados académicos del grupo.
                </p>
            </a>

            

            <a href="{{ route('grupos.reportes.asistencia', $grupo) }}"
               class="rounded-xl border border-slate-200 bg-white p-5 hover:border-emerald-400">
                <h3 class="font-semibold text-slate-900">
                    Constancia de asistencia
                </h3>

                <p class="mt-1 text-sm text-slate-500">
                    Reporte de asistencia por sesión.
                </p>
            </a>
        </div>
    </div>
</x-app-layout>