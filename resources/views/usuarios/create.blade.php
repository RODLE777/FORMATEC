<x-app-layout title="Nuevo usuario">
    <div class="max-w-lg rounded-xl bg-white p-6 shadow-sm border border-slate-100">
        <form method="POST" action="{{ route('usuarios.store') }}" class="space-y-4">
            @csrf
            @include('usuarios._form')
            <button class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                Guardar usuario
            </button>
        </form>
    </div>
</x-app-layout>
