
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

        /* ENCABEZADO */

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
            font-size: 16px;
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

        /* TABLA DE ASISTENCIA */

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
            padding: 7px 4px;
            text-align: center;
        }

        .tabla td {
            border: 1px solid #dbe2ea;
            padding: 6px 4px;
            font-size: 8.5px;
            vertical-align: middle;
        }

        .tabla tbody tr:nth-child(even) {
            background: #f8fafc;
        }

        .tabla .estudiante {
            text-align: left;
            min-width: 130px;
        }

        .tabla .centro {
            text-align: center;
        }

        .tabla .porcentaje {
            text-align: center;
            font-weight: bold;
            color: #193d73;
            background: #eef5fb;
        }

        .presente {
            color: #16803c;
            font-weight: bold;
        }

        .ausente {
            color: #b91c1c;
            font-weight: bold;
        }

        .sin-registro {
            color: #94a3b8;
        }

        /* LEYENDA */

        .leyenda {
            margin-top: 12px;
            font-size: 8px;
            color: #64748b;
        }

        .leyenda strong {
            color: #193d73;
        }

        .pie {
            margin-top: 7px;
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
                    CONSTANCIA DE ASISTENCIA
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
    </div>

    {{-- TABLA DE ASISTENCIA --}}
    <table class="tabla">
        <thead>
            <tr>
                <th class="estudiante">
                    Estudiante
                </th>

                @foreach($grupo->sesiones as $sesion)
                    <th class="centro">
                        S{{ $sesion->numero_sesion }}
                        <br>
                        {{ $sesion->fecha->format('d/m') }}
                    </th>
                @endforeach

                <th class="centro">
                    Asistencia
                </th>
            </tr>
        </thead>

        <tbody>
            @forelse($grupo->inscripciones as $inscripcion)
                <tr>
                    <td class="estudiante">
                        {{ $inscripcion->estudiante->nombre_completo }}
                    </td>

                    @foreach($grupo->sesiones as $sesion)
                        @php
                            $asistio = $inscripcion->asistencias
                                ->firstWhere('sesion_id', $sesion->id)?->asistio;
                        @endphp

                        <td class="centro">
                            @if($asistio === null)
                                <span class="sin-registro">—</span>
                            @elseif($asistio)
                                <span class="presente">P</span>
                            @else
                                <span class="ausente">A</span>
                            @endif
                        </td>
                    @endforeach

                    <td class="porcentaje">
                        @php
                            $porcentaje = $inscripcion->porcentajeAsistencia();
                        @endphp

                        {{ $porcentaje === null ? '—' : $porcentaje . '%' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td
                        colspan="{{ $grupo->sesiones->count() + 2 }}"
                        class="centro"
                        style="padding: 15px;"
                    >
                        No hay estudiantes inscritos en este grupo.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- LEYENDA --}}
    <div class="leyenda">
        <strong>P</strong> = Presente
        &nbsp;&nbsp; | &nbsp;&nbsp;
        <strong>A</strong> = Ausente
        &nbsp;&nbsp; | &nbsp;&nbsp;
        <strong>—</strong> = Sin registro
    </div>

    <div class="pie">
        FORMATEC · {{ $grupo->codigo_grupo }}
    </div>

</body>
</html>
