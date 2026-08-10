<x-app-layout title="Importacion masiva">
    <div
    class="space-y-6"
    x-data="{
        tipo: '{{ old('tipo', 'ESTUDIANTES') }}'
    }"
>


    <div class="max-w-2xl rounded-xl bg-white p-6 shadow-sm border border-slate-100">

        <div class="mb-6">
            <h2 class="text-lg font-semibold text-slate-900">
                Importar datos
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Selecciona el tipo de información que deseas importar.
            </p>
        </div>

        {{-- Mensaje general de error --}}
        @if ($errors->any())
            <div class="mb-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700 border border-red-200">
                {{ $errors->first() }}
            </div>
        @endif

        {{-- Mensaje de éxito --}}
        @if (session('success'))
            <div class="mb-4 rounded-lg bg-emerald-50 px-4 py-3 text-sm text-emerald-700 border border-emerald-200">
                {{ session('success') }}
            </div>
        @endif

        <form
            method="POST"
            action="{{ route('importaciones.store') }}"
            enctype="multipart/form-data"
            class="space-y-4"
        >
            @csrf

            {{-- Tipo de importación --}}
            <div>

                <label
                    for="tipo"
                    class="block text-sm font-medium text-slate-700"
                >
                    Tipo de importación
                </label>

                <select
                    id="tipo"
                    name="tipo"
                    x-model="tipo"
                    class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm
                           focus:border-slate-500 focus:ring-slate-500"
                >
                    <option value="ESTUDIANTES">
                        Estudiantes nuevos
                    </option>

                    <option value="CURSOS">
                        Cursos (catálogo)
                    </option>

                    <option value="NOTAS">
                        Notas de estudiantes ya inscritos
                    </option>

                    <option value="HISTORICO">
                        Histórico (cursos ya finalizados antes del sistema)
                    </option>
                </select>

                @error('tipo')
                    <p class="mt-1 text-xs text-red-600">
                        {{ $message }}
                    </p>
                @enderror

            </div>

            {{-- Información según el tipo --}}
            <div class="rounded-lg bg-slate-50 p-3 text-xs text-slate-500">

                {{-- Estudiantes --}}
                <template x-if="tipo === 'ESTUDIANTES'">
                    <span>
                        Columnas:
                        nombres, apellidos, sexo (MASCULINO/FEMENINO),
                        fecha_nacimiento, dui, correo, telefono_celular.
                    </span>
                </template>

                {{-- Cursos --}}
                <template x-if="tipo === 'CURSOS'">
                    <span>
                        Columnas:
                        nombre, descripcion.
                    </span>
                </template>

                {{-- Notas --}}
                <template x-if="tipo === 'NOTAS'">
                    <span>
                        Columnas:
                        codigo_grupo, anio, codigo_formatec,
                        numero_evaluacion, nota, nombre_evaluacion.
                        El estudiante debe estar ya inscrito en ese grupo.
                    </span>
                </template>

                {{-- Histórico --}}
                <template x-if="tipo === 'HISTORICO'">
                    <span>
                        Columnas:
                        nombres, apellidos, sexo, fecha_nacimiento,
                        dui, curso, codigo_grupo, anio, mes,
                        nota_final, resultado_final
                        (GRADUADO/DESERTADO/REPROBADO),
                        fecha_inscripcion.
                    </span>
                </template>

            </div>

            {{-- Archivo --}}
            <div>

                <label
                    for="archivo"
                    class="block text-sm font-medium text-slate-700"
                >
                    Archivo
                </label>

                <input
                    id="archivo"
                    type="file"
                    name="archivo"
                    accept=".xlsx,.xls,.csv"
                    required
                    class="mt-1 block w-full text-sm text-slate-600
                           file:mr-3 file:rounded-lg file:border-0
                           file:bg-slate-100 file:px-3 file:py-2
                           file:text-sm file:font-medium
                           hover:file:bg-slate-200"
                >

                @error('archivo')
                    <p class="mt-1 text-xs text-red-600">
                        {{ $message }}
                    </p>
                @enderror

            </div>

            {{-- Botón --}}
            <div class="pt-2">

                <button
                    type="submit"
                    class="rounded-lg bg-slate-900 px-4 py-2 text-sm
                           font-semibold text-white
                           hover:bg-slate-800 transition duration-200"
                >
                    Importar
                </button>

            </div>

        </form>

    </div>


    

    <div>

        <div class="mb-3">

            <h3 class="font-semibold text-slate-900">
                Historial de importaciones
            </h3>

            <p class="mt-1 text-sm text-slate-500">
                Registro de los archivos importados al sistema.
            </p>

        </div>


        <div class="space-y-3">

            @forelse ($importaciones as $importacion)

                <div
                    class="rounded-xl bg-white p-4 border border-slate-100
                           shadow-sm text-sm"
                >

                    {{-- Cabecera --}}
                    <div class="flex items-center justify-between gap-4">

                        <div class="min-w-0">

                            {{-- Nombre del archivo --}}
                            <span
                                class="font-medium text-slate-900 break-all"
                            >
                                {{ $importacion->archivo_nombre }}
                            </span>

                            {{-- Fecha y usuario --}}
                            <span class="text-slate-400">

                                ·

                                @php
                                    $fechaImportacion = $importacion->created_at;

                                    if ($fechaImportacion instanceof \Carbon\CarbonInterface) {
                                        $fechaFormateada = $fechaImportacion->format('d/m/Y H:i');
                                    } elseif (!empty($fechaImportacion)) {
                                        try {
                                            $fechaFormateada = \Carbon\Carbon::parse($fechaImportacion)->format('d/m/Y H:i');
                                        } catch (\Exception $e) {
                                            $fechaFormateada = $fechaImportacion;
                                        }
                                    } else {
                                        $fechaFormateada = 'Sin fecha';
                                    }
                                @endphp

                                {{ $fechaFormateada }}

                                ·

                                {{ $importacion->usuario->name ?? 'Usuario desconocido' }}

                            </span>

                        </div>


                        {{-- Estado --}}
                        <span
                            class="shrink-0 rounded-full px-2 py-0.5 text-xs
                            {{
                                $importacion->estado === 'COMPLETADO'
                                    ? 'bg-emerald-50 text-emerald-700'
                                    : 'bg-red-50 text-red-700'
                            }}"
                        >
                            {{ $importacion->estado }}
                        </span>

                    </div>


                    {{-- Cantidad de filas --}}
                    <p class="mt-2 text-slate-600">

                        {{ $importacion->filas_importadas }}

                        de

                        {{ $importacion->total_filas }}

                        filas importadas

                        @if ($importacion->filas_error > 0)

                            ·

                            <span class="text-red-600">
                                {{ $importacion->filas_error }}
                                con error
                            </span>

                        @endif

                    </p>


                    {{-- Detalle de errores --}}
                    @if ($importacion->detalle_errores)

                        <details class="mt-2">

                            <summary
                                class="cursor-pointer text-xs text-slate-500
                                       hover:text-slate-700"
                            >
                                Ver detalle de errores
                            </summary>

                            <ul
                                class="mt-2 space-y-1 text-xs text-red-600
                                       list-disc list-inside"
                            >

                                @foreach ($importacion->detalle_errores as $err)

                                    <li>
                                        Fila {{ $err['fila'] ?? '?' }}:

                                        {{ $err['error'] ?? 'Error desconocido' }}
                                    </li>

                                @endforeach

                            </ul>

                        </details>

                    @endif

                </div>

            @empty

                <div
                    class="rounded-xl bg-white p-6 border border-slate-100
                           text-center shadow-sm"
                >

                    <p class="text-sm text-slate-400">
                        Aún no se ha importado nada.
                    </p>

                </div>

            @endforelse

        </div>


        {{-- Paginación --}}
        @if ($importaciones->hasPages())

            <div class="mt-4">
                {{ $importaciones->links() }}
            </div>

        @endif

    </div>

</div>
</x-app-layout>
