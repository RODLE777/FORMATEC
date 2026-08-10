<x-app-layout title="Buscar">
    <form method="GET" class="mb-6 max-w-xl">
        <input type="text" name="q" value="{{ $q }}" autofocus placeholder="Buscar estudiante, grupo o curso..."
               class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm">
    </form>

    @if ($q === '')
        <p class="text-slate-400 text-sm">Escribe un nombre, código o DUI para buscar.</p>
    @else
        @if ($estudiantes->isEmpty() && $grupos->isEmpty() && $cursos->isEmpty())
            <p class="text-slate-400 text-sm">Sin resultados para "{{ $q }}".</p>
        @endif

        @if ($estudiantes->isNotEmpty())
            <div class="mb-6">
                <p class="text-xs uppercase tracking-wider text-slate-500 mb-2">Estudiantes</p>
                <div class="rounded-xl border border-slate-200 bg-white divide-y divide-slate-100">
                    @foreach ($estudiantes as $estudiante)
                        <a href="{{ route('estudiantes.show', $estudiante) }}" class="block px-4 py-3 hover:bg-slate-50">
                            <span class="font-medium text-slate-900">{{ $estudiante->nombre_completo }}</span>
                            <span class="text-xs text-slate-400 font-mono ml-2">{{ $estudiante->codigo_formatec }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        @if ($grupos->isNotEmpty())
            <div class="mb-6">
                <p class="text-xs uppercase tracking-wider text-slate-500 mb-2">Grupos</p>
                <div class="rounded-xl border border-slate-200 bg-white divide-y divide-slate-100">
                    @foreach ($grupos as $grupo)
                        <a href="{{ route('grupos.show', $grupo) }}" class="block px-4 py-3 hover:bg-slate-50">
                            <span class="font-medium text-slate-900">{{ $grupo->codigo_grupo }}</span>
                            <span class="text-xs text-slate-400 ml-2">{{ $grupo->curso->nombre }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        @if ($cursos->isNotEmpty())
            <div class="mb-6">
                <p class="text-xs uppercase tracking-wider text-slate-500 mb-2">Cursos</p>
                <div class="rounded-xl border border-slate-200 bg-white divide-y divide-slate-100">
                    @foreach ($cursos as $curso)
                        <a href="{{ route('cursos.edit', $curso) }}" class="block px-4 py-3 hover:bg-slate-50">
                            {{ $curso->nombre }}
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    @endif
</x-app-layout>
