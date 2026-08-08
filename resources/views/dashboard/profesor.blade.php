<x-app-layout title="Mi panel">
    <div class="grid gap-4 sm:grid-cols-3">
        <div class="rounded-xl bg-white p-5 shadow-sm border border-slate-100">
            <p class="text-sm text-slate-500">Mis grupos</p>
            <p class="mt-1 text-3xl font-bold text-slate-900">{{ $totalGrupos }}</p>
        </div>
        <div class="rounded-xl bg-white p-5 shadow-sm border border-slate-100">
            <p class="text-sm text-slate-500">Grupos en curso</p>
            <p class="mt-1 text-3xl font-bold text-slate-900">{{ $gruposEnCurso }}</p>
        </div>
        <div class="rounded-xl bg-white p-5 shadow-sm border border-slate-100">
            <p class="text-sm text-slate-500">Estudiantes a mi cargo</p>
            <p class="mt-1 text-3xl font-bold text-slate-900">{{ $totalEstudiantes }}</p>
        </div>
    </div>

    <div class="mt-8 rounded-xl bg-white p-5 shadow-sm border border-slate-100">
        <p class="text-sm text-slate-500">
            Asistencia y notas de tus grupos se habilitan en la Fase 3 del proyecto.
        </p>
    </div>
</x-app-layout>
