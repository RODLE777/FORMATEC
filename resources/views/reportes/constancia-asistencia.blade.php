<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Helvetica, Arial, sans-serif; font-size: 10px; color: #1e293b; }
        h1 { font-size: 16px; margin-bottom: 2px; }
        p.sub { color: #64748b; margin-top: 0; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #cbd5e1; padding: 5px 6px; text-align: left; }
        th { background: #f1f5f9; text-transform: uppercase; font-size: 8px; color: #475569; }
        .center { text-align: center; }
        .footer { margin-top: 24px; font-size: 9px; color: #94a3b8; }
    </style>
</head>
<body>
    <h1>Constancia de asistencia — {{ $grupo->curso->nombre }}</h1>
    <p class="sub">Grupo {{ $grupo->codigo_grupo }} · {{ $grupo->mes }}/{{ $grupo->anio }}</p>

    <table>
        <thead>
            <tr>
                <th>Estudiante</th>
                @foreach ($grupo->sesiones as $sesion)
                    <th class="center">S{{ $sesion->numero_sesion }}<br>{{ $sesion->fecha->format('d/m') }}</th>
                @endforeach
                <th class="center">%</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($grupo->inscripciones as $inscripcion)
                <tr>
                    <td>{{ $inscripcion->estudiante->nombre_completo }}</td>
                    @foreach ($grupo->sesiones as $sesion)
                        @php $asistio = $inscripcion->asistencias->firstWhere('sesion_id', $sesion->id)?->asistio; @endphp
                        <td class="center">{{ $asistio === null ? '—' : ($asistio ? 'P' : 'A') }}</td>
                    @endforeach
                    <td class="center">{{ $inscripcion->porcentajeAsistencia() ?? '—' }}%</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p class="footer">P = presente, A = ausente. Generado el {{ now()->format('d/m/Y H:i') }} — FORMATEC, Cuscatlan Sur.</p>
</body>
</html>
