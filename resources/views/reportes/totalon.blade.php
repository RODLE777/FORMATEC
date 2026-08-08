<x-app-layout title="Reporte TOTALON">
    <div class="mb-4 flex flex-wrap items-end justify-between gap-3">
        <form method="GET" class="flex gap-2 items-end">
            <div>
                <label class="block text-xs font-medium text-slate-500">Año</label>
                <select name="anio" onchange="this.form.submit()" class="mt-1 rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    @foreach ($aniosDisponibles as $a)
                        <option value="{{ $a }}" {{ $anio == $a ? 'selected' : '' }}>{{ $a }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500">Periodo</label>
                <select name="periodo" onchange="this.form.submit()" class="mt-1 rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    <option value="anual" {{ $periodo === 'anual' ? 'selected' : '' }}>Consolidado anual</option>
                    <option value="mensual" {{ $periodo === 'mensual' ? 'selected' : '' }}>Detalle mensual</option>
                </select>
            </div>
        </form>

        <a href="{{ route('totalon.pdf', ['anio' => $anio, 'periodo' => $periodo]) }}"
           class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
            Descargar PDF
        </a>
    </div>

    <p class="mb-3 text-sm text-slate-500">
        Matricula, desercion y graduacion por curso — {{ $periodo === 'mensual' ? 'detalle mensual' : 'consolidado anual' }} {{ $anio }}.
        Este es el unico reporte que cruza varios grupos; el resto de reportes se genera desde cada grupo individual.
    </p>

    <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-left text-xs uppercase text-slate-500">
                <tr>
                    <th class="px-3 py-3">Curso</th>
                    @if ($periodo === 'mensual') <th class="px-3 py-3">Mes</th> @endif
                    <th class="px-3 py-3 text-center"># Grupos</th>
                    <th class="px-3 py-3 text-center">Iniciaron M</th>
                    <th class="px-3 py-3 text-center">Iniciaron F</th>
                    <th class="px-3 py-3 text-center">Iniciaron Total</th>
                    <th class="px-3 py-3 text-center">Desertaron</th>
                    <th class="px-3 py-3 text-center">Graduados</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($filas as $fila)
                    <tr>
                        <td class="px-3 py-3 font-medium text-slate-900">{{ $fila->curso }}</td>
                        @if ($periodo === 'mensual') <td class="px-3 py-3 text-slate-600">{{ $fila->mes }}</td> @endif
                        <td class="px-3 py-3 text-center">{{ $fila->numero_de_cursos }}</td>
                        <td class="px-3 py-3 text-center">{{ $fila->iniciaron_masculino }}</td>
                        <td class="px-3 py-3 text-center">{{ $fila->iniciaron_femenino }}</td>
                        <td class="px-3 py-3 text-center font-semibold">{{ $fila->iniciaron_total }}</td>
                        <td class="px-3 py-3 text-center text-red-600">{{ $fila->desertados_total }}</td>
                        <td class="px-3 py-3 text-center text-emerald-600">{{ $fila->graduados_total }}</td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="px-3 py-6 text-center text-slate-400">Sin datos para este periodo.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-app-layout>
