<x-app-layout title="Nuevo profesor">
    <div class="max-w-2xl rounded-xl bg-white p-6 shadow-sm border border-slate-100">
        <form method="POST" action="{{ route('profesores.store') }}" class="space-y-4">
            @csrf
            @include('profesores._form')
            <button class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                Guardar profesor
            </button>
        </form>
    </div>
</x-app-layout>
