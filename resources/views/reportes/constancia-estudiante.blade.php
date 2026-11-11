
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">

    <style>
        @page {
            size: 792pt 612pt;
            margin: 0;
        }

        * {
            box-sizing: border-box;
        }

        html, body {
            margin: 0;
            padding: 0;
            width: 792pt;
            height: 612pt;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            background: #fffdf8;
            color: #183762;
        }

        .pagina {
            position: relative;
            width: 792pt;
            height: 612pt;
            overflow: hidden;
            background: #fffdf8;
        }

        /* MARCOS */

        .marco-exterior {
            position: absolute;
            top: 13pt;
            left: 13pt;
            width: 766pt;
            height: 586pt;
            border: 5pt solid #193d73;
        }

        .marco-dorado {
            position: absolute;
            top: 23pt;
            left: 23pt;
            width: 746pt;
            height: 566pt;
            border: 2pt solid #c7a457;
        }

        .marco-interior {
            position: absolute;
            top: 31pt;
            left: 31pt;
            width: 730pt;
            height: 550pt;
            border: 1pt solid #e6d8b7;
        }

        /* ORNAMENTOS */

        .esquina {
            position: absolute;
            width: 32pt;
            height: 32pt;
            border-color: #c7a457;
            border-style: solid;
        }

        .esquina-si {
            top: 40pt;
            left: 40pt;
            border-width: 2pt 0 0 2pt;
        }

        .esquina-sd {
            top: 40pt;
            left: 720pt;
            border-width: 2pt 2pt 0 0;
        }

        .esquina-ii {
            top: 540pt;
            left: 40pt;
            border-width: 0 0 2pt 2pt;
        }

        .esquina-id {
            top: 540pt;
            left: 720pt;
            border-width: 0 2pt 2pt 0;
        }

        .detalle-superior {
            position: absolute;
            top: 34pt;
            left: 316pt;
            width: 160pt;
            text-align: center;
            font-size: 11pt;
            letter-spacing: 9pt;
            color: #c7a457;
        }

        /* ENCABEZADO */

        .encabezado {
            position: absolute;
            top: 55pt;
            left: 66pt;
            width: 660pt;
            height: 84pt;
        }

        .tabla-logos {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .tabla-logos td {
            vertical-align: middle;
        }

        .celda-formatec {
            width: 27%;
            text-align: left;
        }

        .celda-institucion {
            width: 49%;
            text-align: center;
        }

        .celda-municipio {
            width: 24%;
            text-align: right;
        }

        .logo-formatec {
            width: 170pt;
            height: auto;
        }

        .logo-municipio {
            width: 76pt;
            height: auto;
        }

        .nombre-institucion {
            font-size: 9pt;
            font-weight: bold;
            letter-spacing: 1.1pt;
            line-height: 1.7;
            color: #193d73;
        }

        .lema {
            margin-top: 6pt;
            font-size: 7.5pt;
            color: #8090a4;
            line-height: 1.5;
        }

        /* TITULO */

        .titulo {
            position: absolute;
            top: 153pt;
            left: 66pt;
            width: 660pt;
            text-align: center;
            font-family: DejaVu Serif, serif;
            font-size: 35pt;
            font-weight: bold;
            letter-spacing: 5pt;
            color: #193d73;
            line-height: 1.2;
        }

        .subtitulo {
            position: absolute;
            top: 204pt;
            left: 66pt;
            width: 660pt;
            text-align: center;
            font-size: 11pt;
            font-weight: bold;
            letter-spacing: 4pt;
            color: #ad8840;
        }

        .linea-izquierda {
            position: absolute;
            top: 231pt;
            left: 258pt;
            width: 126pt;
            border-top: 1pt solid #c7a457;
        }

        .linea-derecha {
            position: absolute;
            top: 231pt;
            left: 408pt;
            width: 126pt;
            border-top: 1pt solid #c7a457;
        }

        .rombo {
            position: absolute;
            top: 226pt;
            left: 391pt;
            width: 10pt;
            height: 10pt;
            background: #c7a457;
            transform: rotate(45deg);
        }

        /* CONTENIDO */

        .introduccion {
            position: absolute;
            top: 253pt;
            left: 100pt;
            width: 592pt;
            text-align: center;
            font-size: 10.5pt;
            color: #66788b;
        }

        .nombre {
            position: absolute;
            top: 283pt;
            left: 83pt;
            width: 626pt;
            text-align: center;
            font-family: DejaVu Serif, serif;
            font-size: 25pt;
            font-weight: bold;
            line-height: 1.3;
            color: #193d73;
        }

        .linea-nombre {
            position: absolute;
            top: 324pt;
            left: 176pt;
            width: 440pt;
            border-bottom: 1pt solid #d8bd7e;
        }

        .reconocimiento {
            position: absolute;
            top: 342pt;
            left: 123pt;
            width: 546pt;
            text-align: center;
            font-size: 10pt;
            line-height: 1.65;
            color: #52677c;
        }

        .curso {
            position: absolute;
            top: 385pt;
            left: 103pt;
            width: 586pt;
            text-align: center;
            font-family: DejaVu Serif, serif;
            font-size: 19pt;
            font-weight: bold;
            line-height: 1.35;
            color: #1d70b3;
        }

        /* DATOS */

        .tabla-datos {
            position: absolute;
            top: 433pt;
            left: 105pt;
            width: 582pt;
            border-collapse: collapse;
            table-layout: fixed;
            background: #f8f6ef;
            border-top: 1pt solid #d8bd7e;
            border-bottom: 1pt solid #d8bd7e;
        }

        .tabla-datos td {
            width: 25%;
            text-align: center;
            padding: 10pt 4pt;
        }

        .etiqueta {
            display: block;
            font-size: 7pt;
            color: #8391a1;
            letter-spacing: 0.5pt;
            text-transform: uppercase;
            margin-bottom: 5pt;
        }

        .valor {
            display: block;
            font-size: 9pt;
            font-weight: bold;
            color: #193d73;
        }

        /* FIRMAS */

        .tabla-firmas {
            position: absolute;
            top: 516pt;
            left: 116pt;
            width: 560pt;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .tabla-firmas td {
            width: 50%;
            text-align: center;
            vertical-align: top;
        }

        .linea-firma {
            width: 188pt;
            border-top: 1pt solid #193d73;
            margin: 0 auto 7pt;
        }

        .cargo {
            font-size: 9pt;
            font-weight: bold;
            color: #193d73;
        }

        .detalle-cargo {
            margin-top: 4pt;
            font-size: 7pt;
            color: #8b98a8;
        }

        /* PIE */

        .pie-institucional {
            position: absolute;
            top: 568pt;
            left: 110pt;
            width: 572pt;
            text-align: center;
            font-size: 7pt;
            letter-spacing: 0.7pt;
            color: #8b98a8;
        }

        .banda-azul {
            position: absolute;
            top: 586pt;
            left: 23pt;
            width: 746pt;
            height: 5pt;
            background: #193d73;
        }

        .banda-verde {
            position: absolute;
            top: 586pt;
            left: 23pt;
            width: 235pt;
            height: 5pt;
            background: #93c900;
        }

        .banda-dorada {
            position: absolute;
            top: 586pt;
            left: 619pt;
            width: 150pt;
            height: 5pt;
            background: #c7a457;
        }
    </style>
</head>

<body>
    <div class="pagina">

        {{-- MARCOS --}}
        <div class="marco-exterior"></div>
        <div class="marco-dorado"></div>
        <div class="marco-interior"></div>

        {{-- ORNAMENTOS --}}
        <div class="esquina esquina-si"></div>
        <div class="esquina esquina-sd"></div>
        <div class="esquina esquina-ii"></div>
        <div class="esquina esquina-id"></div>

        <div class="detalle-superior">
            &#10022; &#10022; &#10022;
        </div>

        {{-- LOGOTIPOS --}}
        <div class="encabezado">
            <table class="tabla-logos">
                <tr>
                    <td class="celda-formatec">
                        <img
                            src="{{ public_path('images/formatec2024.png') }}"
                            class="logo-formatec"
                            alt="FORMATEC"
                        >
                    </td>

                    <td class="celda-institucion">
                        <div class="nombre-institucion">
                            FORMACIÓN TECNOLÓGICA<br>
                            DE CUSCATLÁN SUR
                        </div>

                        <div class="lema">
                            Formación para el desarrollo de nuestra comunidad
                        </div>
                    </td>

                    <td class="celda-municipio">
                        <img
                            src="{{ public_path('images/logo.png') }}"
                            class="logo-municipio"
                            alt="Municipio de Cuscatlán Sur"
                        >
                    </td>
                </tr>
            </table>
        </div>

        {{-- TITULO --}}
        <div class="titulo">
            DIPLOMA
        </div>

        <div class="subtitulo">
            DE RECONOCIMIENTO
        </div>

        <div class="linea-izquierda"></div>
        <div class="rombo"></div>
        <div class="linea-derecha"></div>

        {{-- ESTUDIANTE --}}
        <div class="introduccion">
            Formación Tecnológica de Cuscatlán Sur otorga
            el presente reconocimiento a:
        </div>

        <div class="nombre">
            {{ $inscripcion->estudiante->nombre_completo }}
        </div>

        <div class="linea-nombre"></div>

        {{-- TEXTO --}}
        <div class="reconocimiento">
            Por haber culminado satisfactoriamente su formación,
            demostrando dedicación, perseverancia y compromiso
            con el aprendizaje durante el desarrollo del curso:
        </div>

        {{-- CURSO --}}
        <div class="curso">
            {{ $grupo->curso->nombre }}
        </div>

        {{-- DATOS ACADEMICOS --}}
        <table class="tabla-datos">
            <tr>
                <td>
                    <span class="etiqueta">
                        Código institucional
                    </span>

                    <span class="valor">
                        {{ $inscripcion->estudiante->codigo_formatec }}
                    </span>
                </td>

                <td>
                    <span class="etiqueta">
                        Grupo
                    </span>

                    <span class="valor">
                        {{ $grupo->codigo_grupo }}
                    </span>
                </td>

                <td>
                    <span class="etiqueta">
                        Período académico
                    </span>

                    <span class="valor">
                        {{ $grupo->mes }}/{{ $grupo->anio }}
                    </span>
                </td>

                <td>
                    <span class="etiqueta">
                        Calificación final
                    </span>

                    <span class="valor">
                        {{ number_format((float) $inscripcion->nota_final, 2) }}
                    </span>
                </td>
            </tr>
        </table>

        {{-- FIRMAS --}}
        <table class="tabla-firmas">
            <tr>
                <td>
                    <div class="linea-firma"></div>

                    <div class="cargo">
                        Dirección FORMATEC
                    </div>

                    <div class="detalle-cargo">
                        Formación Tecnológica de Cuscatlán Sur
                    </div>
                </td>

                <td>
                    <div class="linea-firma"></div>

                    <div class="cargo">
                        Representación Municipal
                    </div>

                    <div class="detalle-cargo">
                        Municipio de Cuscatlán Sur
                    </div>
                </td>
            </tr>
        </table>

        {{-- PIE --}}
        <div class="pie-institucional">
            MUNICIPIO DE CUSCATLÁN SUR
            &nbsp; • &nbsp;
            FORMACIÓN TECNOLÓGICA
            &nbsp; • &nbsp;
            {{ $inscripcion->estudiante->codigo_formatec }}
        </div>

        <div class="banda-azul"></div>
        <div class="banda-verde"></div>
        <div class="banda-dorada"></div>

    </div>
</body>
</html>
