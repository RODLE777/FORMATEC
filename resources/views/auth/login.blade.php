<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ingresar · FORMATEC</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen flex items-center justify-center px-4"
      style="background-image: url('{{ asset('images/fondo.png') }}'); background-size: cover; background-position: center; background-repeat: no-repeat;">

    <div class="w-full max-w-sm rounded-2xl bg-white p-8 shadow-xl">
        <div class="flex justify-center mb-6">
            
            <img src="{{ asset('images/formatec2024.png') }}" alt="FORMATEC" class="h-28 w-auto object-contain" >
        </div>

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
               
                <label class="block text-sm font-medium text-slate-700">Contraseña</label>
                <input type="password" name="password" required
                       class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:ring-slate-500">
            </div>
            <div class="flex items-center justify-between">
                <label class="flex items-center gap-2 text-sm text-slate-600">
                    <input type="checkbox" name="remember" class="rounded border-slate-300 text-slate-900 focus:ring-slate-500"> Recordarme
                </label>
                
            </div>
            
            <button type="submit"
                    class="w-full rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800 transition duration-150 ease-in-out">
                Ingresar
            </button>
        </form>
    </div>
</body>
</html>