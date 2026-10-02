<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="{{ asset('images/icono.png') }}">
    <title>Registro · FORMATEC</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-100 text-slate-800 antialiased font-sans min-h-screen flex flex-col">
    <header class="bg-slate-900 text-white shadow">
        <div class="max-w-4xl mx-auto px-4 py-4 flex items-center justify-between">
            <img src="{{ asset('images/formatec2024.png') }}" alt="FORMATEC" class="h-9 w-auto object-contain">
            <span class="text-xs uppercase tracking-widest text-slate-300">Auto-registro</span>
        </div>
    </header>

    <main class="flex-1 max-w-4xl w-full mx-auto px-4 py-8">
        @if (session('status'))
            <div class="mb-6 rounded-xl bg-emerald-50 px-4 py-3 text-emerald-800 border border-emerald-200">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 rounded-xl bg-red-50 px-4 py-3 text-red-800 border border-red-200 text-sm">
                <p class="font-semibold mb-1">Corrige los siguientes errores:</p>
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{ $slot }}
    </main>

    <footer class="text-center text-xs text-slate-400 py-6">
        FORMATEC · Cojupeque
    </footer>
</body>
</html>