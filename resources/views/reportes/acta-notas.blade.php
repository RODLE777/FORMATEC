
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">

    <style>
        @page {
            size: letter landscape;
            margin: 25px 30px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 9px;
            color: #26364a;
            margin: 0;
        }

        /* ENCABEZADO COMPACTO */

        .encabezado {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .encabezado td {
            border: none;
            padding: 0;
            vertical-align: middle;
        }

        .logo-formatec {
            width: 115px;
            height: auto;
        }

        .logo-municipio {
            width: 48px;
            height: auto;
        }

        .titulo {
            font-family: DejaVu Sans, sans-serif;
            font-size: 17px;
            font-weight: bold;
            text-align: center;
            color: #193d73;
            margin: 0;
        }

        .subtitulo {
            text-align: center;
            font-size: 8px;
            color: #64748b;
            margin-top: 4px;
        }

        .linea {
            border-bottom: 2px solid #1d70b3;
            margin: 8px 0 13px;
        }

        /* INFORMACION DEL GRUPO */

        .info {
            font-size: 9px;
            line-height: 1.8;
            margin-bottom: 13px;
        }

        .info strong {
            color: #193d73;
        }

        /* TABLA DE NOTAS */

        .tabla {
            width: 100%;
            border-collapse: collapse;
            table-layout: auto;
        }

        .tabla thead {
            display: table-header-group;
        }

        .tabla tr {
            page-break-inside: avoid;
        }

        .tabla th {
            background: #edf3f8;
            color: #193d73;
            font-size: 8px;
            font-weight: bold;
            border: 1px solid #cbd5e1;
            padding: 7px 5px;
            text-align: center;
        }

        .tabla td {
            border: 1px solid #dbe2ea;
            padding: 6px 5px;
            font-size: 8.5px;
            vertical-align: middle;
        }

        .tabla tbody tr:nth-child(even) {
            background: #f8fafc;
        }

        .tabla .numero {
            width: 25px;
            text-align: center;
        }

        .tabla .estudiante {
            text-align: left;
        }

        .tabla .centro {
            text-align: center;
        }

        .tabla .nota-final {
            font-weight: bold;
            text-align: center;
            color: #193d73;
        }

        .tabla .peso {
            background: #f8fafc;
            font-size: 7.5px;
            color: #64748b;
            font-style: italic;
            font-weight: normal;
        }

        .tabla .resultado {
            text-align: center;
            font-size: 8px;
        }

        /* PIE DISCRETO */

        .pie {
            margin-top: 12px;
            font-size: 8px;
            color: #94a3b8;
            text-align: right;
        }
    </style>
</head>

<body>

    {{-- ENCABEZADO --}}
    <table class="encabezado">
        <tr>
            <td style="width: 25%; text-align: left;">
                <img
                    src="{{ public_path('images/formatec2024.png') }}"
                    class="logo-formatec"
                    alt="FORMATEC"
                >
            </td>

            <td style="width: 55%; text-align: center;">
                <h1 class="titulo">
                    ACTA DE NOTAS
                </h1>

                <div class="subtitulo">
                    Formación Tecnológica de Cuscatlán Sur
                </div>
            </td>

            <td style="width: 20%; text-align: right;">
                <img
                    src="{{ public_path('images/logo.png') }}"
                    class="logo-municipio"
                    alt="Municipio de Cuscatlán Sur"
                >
            </td>
        </tr>
    </table>

    <div class="linea"></div>

    {{-- INFORMACION DEL GRUPO --}}
    <div class="info">
        <strong>Curso:</strong>
        {{ $grupo->curso->nombre }}

        &nbsp;&nbsp; | &nbsp;&nbsp;

        <strong>Grupo:</strong>
        {{ $grupo->codigo_grupo }}

        &nbsp;&nbsp; | &nbsp;&nbsp;

        <strong>Período:</strong>
        {{ $grupo->mes }}/{{ $grupo->anio }}

        <br>

        <strong>Profesor:</strong>
        {{ $grupo->profesor?->nombre_completo ?? 'Sin asignar' }}
    </div>

    {{-- TABLA DE CALIFICACIONES --}}
    <table class="tabla">
        <thead>
            <tr>
                <th class="numero">#</th>

                <th class="estudiante">
                    Estudiante
                </th>

                @foreach($config as $ev)
                    <th class="centro">
                        {{ $ev['nombre'] }}
                    </th>
                @endforeach

                <th class="centro">
                    Nota final
                </th>

                <th class="centro">
                    Resultado
                </th>
            </tr>

            <tr>
                <th class="peso"></th>

                <th class="peso" style="text-align: right;">
                    Peso
                </th>

                @foreach($config as $ev)
                    <th class="peso">
                        {{ number_format((float) $ev['porcentaje'], 2) }}%
                    </th>
                @endforeach

                <th class="peso">
                    100.00%
                </th>

                <th class="peso"></th>
            </tr>
        </thead>

        <tbody>
            @forelse($grupo->inscripciones as $i => $inscripcion)
                <tr>
                    <td class="numero">
                        {{ $i + 1 }}
                    </td>

                    <td class="estudiante">
                        {{ $inscripcion->estudiante->nombre_completo }}
                    </td>

                    @foreach($config as $idx => $ev)
                        @php
                            $numero = $idx + 1;

                            $nota = $inscripcion->evaluaciones
                                ->firstWhere('numero_evaluacion', $numero)?->nota;
                        @endphp

                        <td class="centro">
                            {{ $nota ?? '—' }}
                        </td>
                    @endforeach

                    <td class="nota-final">
                        {{ $inscripcion->nota_final ?? '—' }}
                    </td>

                    <td class="resultado">
                        {{ $inscripcion->resultado_final }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td
                        colspan="{{ count($config) + 4 }}"
                        class="centro"
                        style="padding: 15px;"
                    >
                        No hay estudiantes inscritos.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="pie">
        FORMATEC · {{ $grupo->codigo_grupo }}
    </div>

</body>
</html>
