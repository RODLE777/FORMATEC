<x-app-layout title="Editar curso">
    <div class="max-w-lg rounded-xl bg-white p-6 shadow-sm border border-slate-100">
        <form method="POST" action="{{ route('cursos.update', $curso) }}" class="space-y-4">
            @csrf @method('PUT')
            @include('cursos._form')
            <button class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                Guardar cambios
            </button>
        </form>
    </div>
</x-app-layout>
