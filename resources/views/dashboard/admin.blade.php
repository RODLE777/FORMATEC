<x-app-layout title="Panel principal">
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-xl bg-white p-5 shadow-sm border border-slate-100">
            <p class="text-sm text-slate-500">Estudiantes registrados</p>
            <p class="mt-1 text-3xl font-bold text-slate-900">{{ $totalEstudiantes }}</p>
        </div>
        <div class="rounded-xl bg-white p-5 shadow-sm border border-slate-100">
            <p class="text-sm text-slate-500">Grupos en curso</p>
            <p class="mt-1 text-3xl font-bold text-slate-900">{{ $totalGruposEnCurso }}</p>
        </div>
        <a href="{{ route('resultados.index', ['resultado' => 'GRADUADO']) }}"
           class="rounded-xl bg-white p-5 shadow-sm border border-slate-100 hover:border-slate-300">
            <p class="text-sm text-slate-500">Graduados (historico)</p>
            <p class="mt-1 text-3xl font-bold text-emerald-600">{{ $totalGraduados }}</p>
        </a>
        <a href="{{ route('resultados.index', ['resultado' => 'DESERTADO']) }}"
           class="rounded-xl bg-white p-5 shadow-sm border border-slate-100 hover:border-slate-300">
            <p class="text-sm text-slate-500">Desertados (historico)</p>
            <p class="mt-1 text-3xl font-bold text-red-500">{{ $totalDesertados }}</p>
        </a>
    </div>

    <div class="mt-8 grid gap-4 lg:grid-cols-2">
        <div class="rounded-xl bg-white p-5 shadow-sm border border-slate-100">
            <p class="text-sm font-semibold text-slate-700 mb-3">Matricula por mes ({{ date('Y') }})</p>
            <canvas id="chartMatricula" height="180"></canvas>
        </div>
        <div class="rounded-xl bg-white p-5 shadow-sm border border-slate-100">
            <p class="text-sm font-semibold text-slate-700 mb-3">Resultados (historico)</p>
            <canvas id="chartResultados" height="180"></canvas>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const meses = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];

            new Chart(document.getElementById('chartMatricula'), {
                type: 'bar',
                data: {
                    labels: meses,
                    datasets: [{
                        label: 'Estudiantes inscritos',
                        data: @json($matriculaMensual->values()),
                        backgroundColor: '#0f172a',
                        borderRadius: 4,
                    }],
                },
                options: {
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true, ticks: { precision: 0 } } },
                },
            });

            new Chart(document.getElementById('chartResultados'), {
                type: 'doughnut',
                data: {
                    labels: ['Graduados', 'Desertados', 'Reprobados', 'En curso'],
                    datasets: [{
                        data: [
                            {{ $resultados['GRADUADO'] }},
                            {{ $resultados['DESERTADO'] }},
                            {{ $resultados['REPROBADO'] }},
                            {{ $resultados['EN_CURSO'] }},
                        ],
                        backgroundColor: ['#059669', '#dc2626', '#f59e0b', '#94a3b8'],
                    }],
                },
                options: { plugins: { legend: { position: 'bottom' } } },
            });
        });
    </script>
</x-app-layout>
