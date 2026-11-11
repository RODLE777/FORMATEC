
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte TOTALON - FORMATEC</title>

    <style>
        @page {
            size: letter landscape;
            margin: 25px 25px 38px 25px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: DejaVu Sans, sans-serif;
            font-size: 8px;
            color: #243449;
        }

        /* ENCABEZADO */

        .header {
            background-color: #17324f;
            color: #ffffff;
            padding: 16px 18px;
            margin-bottom: 12px;
        }

        .header .institucion {
            font-size: 11px;
            font-weight: bold;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }

        .header h1 {
            font-size: 19px;
            font-weight: bold;
            margin: 0 0 5px 0;
        }

        .header .descripcion {
            font-size: 8px;
            color: #dbeafe;
        }

        /* INFORMACION DEL PERIODO */

        .periodo {
            width: 100%;
            border: 1px solid #d5e0eb;
            background-color: #f4f8fc;
            padding: 9px 11px;
            margin-bottom: 12px;
            line-height: 1.6;
        }

        .periodo strong {
            color: #17324f;
        }

        /* TARJETAS DE RESUMEN */

        .resumen {
            width: 100%;
            border-collapse: separate;
            border-spacing: 5px 0;
            margin: 0 0 12px -5px;
            table-layout: fixed;
        }

        .resumen td {
            width: 25%;
            padding: 10px;
            background-color: #f1f5f9;
            border: 1px solid #d8e2ed;
            vertical-align: middle;
        }

        .resumen .etiqueta {
            font-size: 7px;
            color: #53657b;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .resumen .valor {
            font-size: 17px;
            color: #17324f;
            font-weight: bold;
        }

        .resumen .valor.azul {
            color: #2563eb;
        }

        .resumen .valor.naranja {
            color: #ea580c;
        }

        .resumen .valor.verde {
            color: #15803d;
        }

        /* TITULO DE SECCION */

        .seccion {
            font-size: 11px;
            font-weight: bold;
            color: #17324f;
            margin: 13px 0 8px;
        }

        /* TABLA PRINCIPAL */

        .tabla-totalon {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .tabla-totalon th,
        .tabla-totalon td {
            border: 1px solid #c9d6e4;
            padding: 7px 3px;
            text-align: center;
            vertical-align: middle;
            word-wrap: break-word;
        }

        .tabla-totalon thead th {
            color: #ffffff;
            font-size: 7px;
            font-weight: bold;
        }

        .tabla-totalon thead tr:first-child th {
            padding: 10px 3px;
            font-size: 8px;
        }

        .fondo-oscuro {
            background-color: #334155;
        }

        .fondo-azul {
            background-color: #2563eb;
        }

        .fondo-naranja {
            background-color: #ea580c;
        }

        .fondo-verde {
            background-color: #15803d;
        }

        .tabla-totalon tbody tr:nth-child(even) {
            background-color: #f1f5f9;
        }

        .tabla-totalon tbody td:first-child {
            text-align: left;
            padding-left: 8px;
            font-weight: bold;
        }

        .tabla-totalon tbody td {
            font-size: 8px;
        }

        .tabla-totalon tfoot td {
            background-color: #dbeafe;
            color: #17324f;
            font-weight: bold;
            border-top: 2px solid #17324f;
            padding: 10px 3px;
            font-size: 8px;
        }

        .tabla-totalon tfoot td:first-child {
            text-align: left;
            padding-left: 8px;
        }

        .sin-datos {
            text-align: center !important;
            padding: 18px !important;
            font-weight: normal !important;
        }

        /* NOTAS METODOLOGICAS */

        .notas {
            margin-top: 14px;
            padding: 10px 12px;
            border-left: 3px solid #2563eb;
            background-color: #f4f8fc;
            font-size: 7.5px;
            line-height: 1.7;
        }

        .notas strong {
            color: #17324f;
        }

        /* PIE DE PAGINA */

        .footer {
            position: fixed;
            bottom: -24px;
            left: 0;
            right: 0;
            border-top: 1px solid #cbd5e1;
            padding-top: 7px;
            font-size: 7px;
            color: #64748b;
        }

        .footer .derecha {
            float: right;
        }

        thead {
            display: table-header-group;
        }

        tfoot {
            display: table-row-group;
        }

        tr {
            page-break-inside: avoid;
        }
    </style>
</head>

<body>

@php
    /*
    |--------------------------------------------------------------------------
    | COLUMNAS OFICIALES DEL TOTALON
    |--------------------------------------------------------------------------
    */

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

    /*
    |--------------------------------------------------------------------------
    | TOTALES GENERALES
    |--------------------------------------------------------------------------
    */

    $obtenerTotal = function ($campo) use ($totales) {
        return (int) ($totales->{$campo} ?? 0);
    };

    /*
    |--------------------------------------------------------------------------
    | PERIODO DEL REPORTE
    |--------------------------------------------------------------------------
    */

    $periodoTexto = $nombrePeriodo
        ?? ('Año ' . ($anio ?? date('Y')));

    $nombreCurso = $cursoNombre ?? null;

    $fechaGeneracion = now()->format('d/m/Y H:i');
@endphp

<!-- ENCABEZADO -->

<div class="header">

    <div class="institucion">
        FORMATEC · CUSCATLÁN SUR
    </div>

    <h1>
        INFORME TOTALON
    </h1>

    <div class="descripcion">
        Consolidado estadístico de matrícula,
        deserción y graduación por curso
    </div>

</div>

<!-- INFORMACION GENERAL -->

<div class="periodo">

    <strong>Período evaluado:</strong>
    {{ $periodoTexto }}

    @if(!empty($nombreCurso))
        &nbsp; | &nbsp;
        <strong>Curso:</strong>
        {{ $nombreCurso }}
    @endif

    &nbsp; | &nbsp;

    <strong>Fecha de emisión:</strong>
    {{ $fechaGeneracion }}

</div>

<!-- RESUMEN ESTADISTICO -->

<table class="resumen">

    <tr>

        <td>
            <div class="etiqueta">
                GRUPOS FINALIZADOS
            </div>

            <div class="valor">
                {{ $obtenerTotal('numero_de_cursos') }}
            </div>
        </td>

        <td>
            <div class="etiqueta">
                PARTICIPANTES QUE INICIARON
            </div>

            <div class="valor azul">
                {{ $obtenerTotal('iniciaron_total') }}
            </div>
        </td>

        <td>
            <div class="etiqueta">
                DESERTADOS
            </div>

            <div class="valor naranja">
                {{ $obtenerTotal('desertados_total') }}
            </div>
        </td>

        <td>
            <div class="etiqueta">
                GRADUADOS
            </div>

            <div class="valor verde">
                {{ $obtenerTotal('graduados_total') }}
            </div>
        </td>

    </tr>

</table>

<!-- TITULO TABLA -->

<div class="seccion">
    Consolidado por curso
</div>

<!-- TABLA TOTALON -->

<table class="tabla-totalon">

    <colgroup>
        <col style="width: 23%;">
        <col style="width: 9%;">

        @for($i = 0; $i < 9; $i++)
            <col style="width: 7.55%;">
        @endfor
    </colgroup>

    <thead>

        <tr>

            <th class="fondo-oscuro" rowspan="2">
                CURSO
            </th>

            <th class="fondo-oscuro" rowspan="2">
                N.º<br>GRUPOS
            </th>

            <th class="fondo-azul" colspan="3">
                INICIARON
            </th>

            <th class="fondo-naranja" colspan="3">
                DESERTADOS
            </th>

            <th class="fondo-verde" colspan="3">
                GRADUADOS
            </th>

        </tr>

        <tr>

            <th class="fondo-azul">M</th>
            <th class="fondo-azul">F</th>
            <th class="fondo-azul">TOTAL</th>

            <th class="fondo-naranja">M</th>
            <th class="fondo-naranja">F</th>
            <th class="fondo-naranja">TOTAL</th>

            <th class="fondo-verde">M</th>
            <th class="fondo-verde">F</th>
            <th class="fondo-verde">TOTAL</th>

        </tr>

    </thead>

    <tbody>

        @forelse($filas as $fila)

            <tr>

                <td>
                    {{ $fila->curso }}
                </td>

                @foreach($campos as $campo)

                    <td>
                        {{ (int) ($fila->{$campo} ?? 0) }}
                    </td>

                @endforeach

            </tr>

        @empty

            <tr>
                <td colspan="11" class="sin-datos">
                    No existen grupos finalizados
                    para el período seleccionado.
                </td>
            </tr>

        @endforelse

    </tbody>

    <tfoot>

        <tr>

            <td>
                TOTAL GENERAL
            </td>

            @foreach($campos as $campo)

                <td>
                    {{ $obtenerTotal($campo) }}
                </td>

            @endforeach

        </tr>

    </tfoot>

</table>

<!-- CRITERIOS DEL REPORTE -->

<div class="notas">

    <strong>CRITERIOS DEL INFORME</strong>

    <br>

    <strong>Grupos incluidos:</strong>
    Únicamente grupos con estado FINALIZADO cuya fecha
    de finalización corresponde al período seleccionado.

    <br>

    <strong>Iniciaron:</strong>
    Número de inscripciones registradas en los grupos incluidos.

    <br>

    <strong>Desertados:</strong>
    Inscripciones con resultado académico DESERTADO.

    <br>

    <strong>Graduados:</strong>
    Inscripciones con resultado académico GRADUADO.

    <br>

    <strong>Importante:</strong>
    Los estudiantes reprobados o con resultados pendientes
    no se contabilizan como graduados.

    <br>

    <strong>M / F:</strong>
    Masculino / Femenino.

</div>

<!-- PIE DE PAGINA -->

<div class="footer">

    FORMATEC — Informe estadístico TOTALON

    <span class="derecha">
        Generado el {{ $fechaGeneracion }}
    </span>

</div>

</body>
</html>
