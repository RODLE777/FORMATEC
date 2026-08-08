<x-app-layout title="Editar grupo">
    <div class="max-w-3xl rounded-xl bg-white p-6 shadow-sm border border-slate-100">
        <form method="POST" action="{{ route('grupos.update', $grupo) }}" class="space-y-4">
            @csrf @method('PUT')
            @include('grupos._form')
        </form>
    </div>
</x-app-layout>
