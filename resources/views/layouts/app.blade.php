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

            <!-- Menú hamburguesa -->
            <div class="relative">
                <button id="menu-toggle" class="flex items-center px-3 py-2 border rounded text-white border-white hover:bg-pink-600 focus:outline-none">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
                <div id="menu-dropdown" class="hidden absolute right-0 mt-2 w-40 bg-white text-slate-800 rounded shadow-lg z-50">
                    @if(!auth()->check())
                        <a href="{{ route('login') }}" class="block px-4 py-2 hover:bg-pink-100">Iniciar sesión</a>
                    @else
                        @php $user = auth()->user(); @endphp
                        @if($user->isGerente() || $user->isAdmin())
                            <a href="{{ route('panel.index') }}" class="block px-4 py-2 hover:bg-pink-100">🏠 Panel</a>
                            <a href="{{ route('vendedores.index') }}" class="block px-4 py-2 hover:bg-pink-100">👥 Vendedores</a>
                            <a href="{{ route('products.index') }}" class="block px-4 py-2 hover:bg-pink-100">🧊 Productos</a>
                            <a href="{{ route('reportes.index') }}" class="block px-4 py-2 hover:bg-pink-100">📊 Reportes</a>
                            <a href="{{ route('ia.index') }}" class="block px-4 py-2 hover:bg-pink-100">🤖 IA</a>
                            <a href="{{ route('config.index') }}" class="block px-4 py-2 hover:bg-pink-100">⚙️ Configuración</a>
                            @if($user->isAdmin())
                                <a href="{{ route('config.critical') }}" class="block px-4 py-2 hover:bg-pink-100">🔒 Config. Crítica</a>
                            @endif
                        @endif
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full text-left px-4 py-2 hover:bg-pink-100">🚪 Cerrar sesión</button>
                        </form>
                    @endif
                </div>
            </div>
            <script>
                const menuToggle = document.getElementById('menu-toggle');
                const menuDropdown = document.getElementById('menu-dropdown');
                document.addEventListener('click', function(e) {
                    if (menuToggle && menuDropdown) {
                        if (menuToggle.contains(e.target)) {
                            menuDropdown.classList.toggle('hidden');
                        } else if (!menuDropdown.contains(e.target)) {
                            menuDropdown.classList.add('hidden');
                        }
                    }
                });
            </script>
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
