<x-app-layout title="Nuevo grupo">
    <div class="max-w-3xl rounded-xl bg-white p-6 shadow-sm border border-slate-100">
        <form method="POST" action="{{ route('grupos.store') }}" class="space-y-4">
            @csrf
            @include('grupos._form')
        </form>
    </div>
</x-app-layout>
