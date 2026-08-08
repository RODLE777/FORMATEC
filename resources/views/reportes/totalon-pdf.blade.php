<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Helvetica, Arial, sans-serif; font-size: 10px; color: #1e293b; }
        h1 { font-size: 16px; margin-bottom: 2px; }
        p.sub { color: #64748b; margin-top: 0; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #cbd5e1; padding: 5px 7px; text-align: left; }
        th { background: #f1f5f9; text-transform: uppercase; font-size: 8px; color: #475569; }
        .center { text-align: center; }
        .footer { margin-top: 24px; font-size: 9px; color: #94a3b8; }
    </style>
</head>
<body>
    <h1>Reporte TOTALON — {{ $anio }}</h1>
    <p class="sub">Matricula, desercion y graduacion consolidada por curso — FORMATEC, Cuscatlan Sur.</p>

    <table>
        <thead>
            <tr>
                <th>Curso</th>
                <th class="center"># Grupos</th>
                <th class="center">Iniciaron M</th>
                <th class="center">Iniciaron F</th>
                <th class="center">Iniciaron Total</th>
                <th class="center">Desertaron</th>
                <th class="center">Graduados</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($filas as $fila)
                <tr>
                    <td>{{ $fila->curso }}</td>
                    <td class="center">{{ $fila->numero_de_cursos }}</td>
                    <td class="center">{{ $fila->iniciaron_masculino }}</td>
                    <td class="center">{{ $fila->iniciaron_femenino }}</td>
                    <td class="center"><strong>{{ $fila->iniciaron_total }}</strong></td>
                    <td class="center">{{ $fila->desertados_total }}</td>
                    <td class="center">{{ $fila->graduados_total }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p class="footer">Generado el {{ now()->format('d/m/Y H:i') }} — FORMATEC, Cuscatlan Sur.</p>
</body>
</html>
