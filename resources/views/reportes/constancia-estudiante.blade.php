<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'Times New Roman', serif; font-size: 13px; color: #1e293b; text-align: center; padding-top: 60px; }
        h1 { font-size: 22px; letter-spacing: 2px; text-transform: uppercase; margin-bottom: 40px; }
        .nombre { font-size: 20px; font-weight: bold; margin: 20px 0; text-decoration: underline; }
        .cuerpo { max-width: 480px; margin: 0 auto; line-height: 1.8; }
        .firma { margin-top: 80px; }
        .firma div { display: inline-block; width: 220px; border-top: 1px solid #1e293b; padding-top: 6px; font-size: 11px; }
    </style>
</head>
<body>
    <h1>FORMATEC — Constancia</h1>

    <div class="cuerpo">
        <p>Formacion Tecnologica de Cuscatlan Sur hace constar que:</p>
        <p class="nombre">{{ $inscripcion->estudiante->nombre_completo }}</p>
        <p>
            con codigo institucional <strong>{{ $inscripcion->estudiante->codigo_formatec }}</strong>,
            finalizo satisfactoriamente el curso de
            <strong>{{ $grupo->curso->nombre }}</strong>,
            grupo {{ $grupo->codigo_grupo }},
            con una nota final de <strong>{{ $inscripcion->nota_final }}</strong>,
            durante el periodo {{ $grupo->mes }}/{{ $grupo->anio }}.
        </p>
    </div>

    <div class="firma">
        <div>Direccion FORMATEC</div>
    </div>

    <p style="margin-top: 60px; font-size: 10px; color: #94a3b8;">
        Emitida el {{ now()->format('d/m/Y') }}.
    </p>
</body>
</html>
