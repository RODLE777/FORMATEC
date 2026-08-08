<x-app-layout title="Nuevo estudiante">
    <div class="max-w-3xl rounded-xl bg-white p-6 shadow-sm border border-slate-100">
        <form method="POST" action="{{ route('estudiantes.store') }}">
            @csrf
            @include('estudiantes._form')
        </form>
    </div>
</x-app-layout>
