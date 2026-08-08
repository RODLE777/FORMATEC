<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ingresar · FORMATEC</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-900 min-h-screen flex items-center justify-center px-4">
    <div class="w-full max-w-sm rounded-2xl bg-white p-8 shadow-xl">
        <h1 class="text-xl font-bold text-slate-900 mb-1">FORMATEC</h1>
        <p class="text-sm text-slate-500 mb-6">Formacion Tecnologica de Cuscatlan Sur</p>

        @if ($errors->any())
            <div class="mb-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700 border border-red-200">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-slate-700">Correo</label>
                <input type="email" name="email" required autofocus
                       class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:ring-slate-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700">Contrasena</label>
                <input type="password" name="password" required
                       class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:ring-slate-500">
            </div>
            <label class="flex items-center gap-2 text-sm text-slate-600">
                <input type="checkbox" name="remember"> Recordarme
            </label>
            <button type="submit"
                    class="w-full rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                Ingresar
            </button>
        </form>
    </div>
</body>
</html>
