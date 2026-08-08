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
        <div class="rounded-xl bg-white p-5 shadow-sm border border-slate-100">
            <p class="text-sm text-slate-500">Graduados (historico)</p>
            <p class="mt-1 text-3xl font-bold text-emerald-600">{{ $totalGraduados }}</p>
        </div>
        <div class="rounded-xl bg-white p-5 shadow-sm border border-slate-100">
            <p class="text-sm text-slate-500">Desertados (historico)</p>
            <p class="mt-1 text-3xl font-bold text-red-500">{{ $totalDesertados }}</p>
        </div>
    </div>

    <div class="mt-8 rounded-xl bg-white p-5 shadow-sm border border-slate-100">
        <p class="text-sm text-slate-500">
            El detalle de matricula/desercion/graduacion por curso y periodo (TOTALON),
            asistencia y notas se activa en las siguientes fases del proyecto.
        </p>
    </div>
</x-app-layout>
