<x-app-layout title="Catalogo geografico">
    <p class="mb-4 text-sm text-slate-500">
        Departamentos, municipios y distritos usados en la ficha de estudiantes.
        Cuscatlan viene precargado con sus 16 municipios; agrega distritos reales
        (caserios, colonias) segun los necesites.
    </p>

    <div class="mb-6 grid gap-4 sm:grid-cols-2">
        <form method="POST" action="{{ route('geografia.municipios.store') }}" class="rounded-xl bg-white p-4 border border-slate-100 shadow-sm">
            @csrf
            <p class="text-sm font-semibold text-slate-900 mb-2">+ Agregar municipio</p>
            <select name="departamento_id" required class="mb-2 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                @foreach ($departamentos as $d)
                    <option value="{{ $d->id }}">{{ $d->nombre }}</option>
                @endforeach
            </select>
            <input type="text" name="nombre" placeholder="Nombre del municipio" required
                   class="mb-2 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            <button class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">Agregar</button>
        </form>

        <form method="POST" action="{{ route('geografia.distritos.store') }}" class="rounded-xl bg-white p-4 border border-slate-100 shadow-sm">
            @csrf
            <p class="text-sm font-semibold text-slate-900 mb-2">+ Agregar distrito</p>
            <select name="municipio_id" required class="mb-2 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                @foreach ($departamentos as $d)
                    @foreach ($d->municipios as $m)
                        <option value="{{ $m->id }}">{{ $d->nombre }} — {{ $m->nombre }}</option>
                    @endforeach
                @endforeach
            </select>
            <input type="text" name="nombre" placeholder="Nombre del distrito/caserio" required
                   class="mb-2 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            <button class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">Agregar</button>
        </form>
    </div>

    <div class="space-y-4">
        @foreach ($departamentos as $departamento)
            <details class="rounded-xl bg-white border border-slate-100 shadow-sm" {{ $loop->first ? 'open' : '' }}>
                <summary class="cursor-pointer px-4 py-3 font-semibold text-slate-900">
                    {{ $departamento->nombre }}
                    <span class="text-slate-400 font-normal text-sm">({{ $departamento->municipios->count() }} municipios)</span>
                </summary>
                <div class="border-t border-slate-100 px-4 py-3 space-y-3">
                    @forelse ($departamento->municipios as $municipio)
                        <div>
                            <div class="flex items-center gap-2">
                                <form method="POST" action="{{ route('geografia.municipios.update', $municipio) }}" class="flex items-center gap-1">
                                    @csrf @method('PUT')
                                    <input type="text" name="nombre" value="{{ $municipio->nombre }}"
                                           class="text-sm font-medium text-slate-700 bg-transparent border-0 p-0 focus:ring-0 w-40">
                                    <button class="text-slate-400 hover:text-slate-700 text-xs" title="Guardar">✓</button>
                                </form>
                                <form method="POST" action="{{ route('geografia.municipios.destroy', $municipio) }}"
                                      onsubmit="return confirm('¿Eliminar el municipio {{ $municipio->nombre }}? Solo funciona si ya no tiene distritos.')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-400 hover:text-red-600 text-xs" title="Eliminar municipio">✕</button>
                                </form>
                            </div>
                            <ul class="mt-1 flex flex-wrap gap-2">
                                @forelse ($municipio->distritos as $distrito)
                                    <li class="flex items-center gap-1 rounded-full bg-slate-100 px-3 py-1 text-xs text-slate-600">
                                        <form method="POST" action="{{ route('geografia.distritos.update', $distrito) }}" class="flex items-center gap-1">
                                            @csrf @method('PUT')
                                            <input type="text" name="nombre" value="{{ $distrito->nombre }}"
                                                   class="w-28 bg-transparent border-0 p-0 text-xs focus:ring-0">
                                            <button class="text-slate-400 hover:text-slate-700" title="Guardar">✓</button>
                                        </form>
                                        <form method="POST" action="{{ route('geografia.distritos.destroy', $distrito) }}"
                                              onsubmit="return confirm('¿Eliminar este distrito?')">
                                            @csrf @method('DELETE')
                                            <button class="text-red-400 hover:text-red-600" title="Eliminar">✕</button>
                                        </form>
                                    </li>
                                @empty
                                    <li class="text-xs text-slate-400">Sin distritos aun.</li>
                                @endforelse
                            </ul>
                        </div>
                    @empty
                        <p class="text-sm text-slate-400">Sin municipios registrados.</p>
                    @endforelse
                </div>
            </details>
        @endforeach
    </div>
</x-app-layout>
