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
                @for ($n = 1; $n <= $grupo->numero_evaluaciones; $n++)
                    <th class="center">Eval. {{ $n }}</th>
                @endfor
                <th class="center">Nota final</th>
                <th class="center">Resultado</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($grupo->inscripciones as $i => $inscripcion)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $inscripcion->estudiante->nombre_completo }}</td>
                    @for ($n = 1; $n <= $grupo->numero_evaluaciones; $n++)
                        <td class="center">{{ $inscripcion->evaluaciones->firstWhere('numero_evaluacion', $n)->nota ?? '—' }}</td>
                    @endfor
                    <td class="center"><strong>{{ $inscripcion->nota_final ?? '—' }}</strong></td>
                    <td class="center">{{ $inscripcion->resultado_final }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p class="footer">Generado el {{ now()->format('d/m/Y H:i') }} — FORMATEC, Cuscatlan Sur.</p>
</body>
</html>
