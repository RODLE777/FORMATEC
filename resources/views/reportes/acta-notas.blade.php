<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Helvetica, Arial, sans-serif; font-size: 11px; color: #1e293b; }
        h1 { font-size: 16px; margin-bottom: 2px; }
        p.sub { color: #64748b; margin-top: 0; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #cbd5e1; padding: 6px 8px; text-align: left; }
        th { background: #f1f5f9; text-transform: uppercase; font-size: 9px; color: #475569; }
        .center { text-align: center; }
        .footer { margin-top: 24px; font-size: 9px; color: #94a3b8; }
    </style>
</head>
<body>
    <h1>Acta de notas — {{ $grupo->curso->nombre }}</h1>
    <p class="sub">
        Grupo {{ $grupo->codigo_grupo }} · {{ $grupo->mes }}/{{ $grupo->anio }} ·
        Profesor: {{ $grupo->profesor?->nombre_completo ?? 'Sin asignar' }}
    </p>

    <table>
    <thead>
        <tr>
            <th>#</th>
            <th>Estudiante</th>
            @foreach ($config as $ev)
                <th class="center">{{ $ev['nombre'] }}</th>
            @endforeach
            <th class="center">Nota final</th>
            <th class="center">Resultado</th>
        </tr>
        <tr>
            <th></th>
            <th style="text-align:right; font-style:italic;">Peso</th>
            @foreach ($config as $ev)
                <th class="center" style="font-style:italic;">{{ number_format($ev['porcentaje'], 2) }}%</th>
            @endforeach
            <th class="center" style="font-style:italic;">100.00%</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @foreach ($grupo->inscripciones as $i => $inscripcion)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $inscripcion->estudiante->nombre_completo }}</td>
                @foreach ($config as $idx => $ev)
                    @php
                        $numero = $idx + 1;
                        $nota = $inscripcion->evaluaciones->firstWhere('numero_evaluacion', $numero)?->nota;
                    @endphp
                    <td class="center">{{ $nota ?? '—' }}</td>
                @endforeach
                <td class="center"><strong>{{ $inscripcion->nota_final ?? '—' }}</strong></td>
                <td class="center">{{ $inscripcion->resultado_final }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

    <p class="footer">Generado el {{ now()->format('d/m/Y H:i') }} — FORMATEC, Cuscatlan Sur.</p>
</body>
</html>
