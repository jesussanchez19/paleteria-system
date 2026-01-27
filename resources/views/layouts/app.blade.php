<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Paletería')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen flex flex-col bg-slate-50 text-slate-900">

    <!-- Navbar -->
    <header class="bg-pink-500 text-white">
        <nav class="max-w-6xl mx-auto px-4 py-3 flex items-center justify-between">
            <a href="{{ url('/') }}" class="font-bold text-lg tracking-wide">
                🍦 Paletería
            </a>

            <div class="flex gap-4 text-sm font-semibold">
                <a class="hover:underline" href="{{ url('/catalogo') }}">Catálogo</a>
                <a class="hover:underline" href="{{ url('/pos') }}">POS</a>
            </div>
        </nav>
    </header>

    <!-- Contenido -->
    <main class="flex-1 max-w-6xl mx-auto w-full px-4 py-6">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="mt-auto bg-white border-t border-slate-200">
        <div class="max-w-6xl mx-auto px-4 py-4 text-center text-sm text-slate-600">
            © {{ date('Y') }} Paletería — Sistema de ventas
        </div>
    </footer>

</body>
</html>
