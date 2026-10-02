<x-app-layout title="Editar estudiante">
    <div class="max-w-3xl rounded-xl bg-white p-6 shadow-sm border border-slate-100">
        <form method="POST" action="{{ route('estudiantes.update', $estudiante) }}">
            @csrf @method('PUT')
            @include('estudiantes._form', ['estudiante' => $estudiante])
        </form>
    </div>
</x-app-layout>