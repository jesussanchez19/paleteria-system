<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Creamyx')</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
        .sidebar-gradient {
            background: linear-gradient(180deg, #1e1b4b 0%, #312e81 100%);
        }
        .accent-gradient {
            background: linear-gradient(135deg, #ec4899 0%, #8b5cf6 100%);
        }
        .nav-item {
            @apply flex items-center gap-3 px-4 py-2.5 rounded-lg text-white/80 hover:bg-white/10 hover:text-white transition-all duration-200 text-sm;
        }
        .nav-item.active {
            @apply bg-white/15 text-white font-semibold;
        }
        .nav-item svg {
            @apply w-4 h-4 flex-shrink-0 stroke-white;
        }
        .nav-section-title {
            @apply px-4 text-xs font-semibold text-white/70 uppercase tracking-wider mb-3;
        }
    </style>
    @stack('styles')
</head>

<body class="min-h-screen bg-slate-50 text-slate-900">
    <div class="flex min-h-screen">
        
        @auth
        <!-- Sidebar -->
        <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 sidebar-gradient transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out">
            <div class="flex flex-col h-full">
                <!-- Logo -->
                <div class="flex items-center gap-3 px-6 py-5 border-b border-white/10">
                    <img src="{{ asset('images/logo-creamyx.png') }}" alt="Creamyx" class="w-12 h-12 rounded-xl shadow-lg object-contain bg-white/10 p-1">
                    <div>
                        <h1 class="text-lg font-bold text-white">Creamyx</h1>
                        <p class="text-xs text-indigo-300">Sistema de gestión</p>
                    </div>
                </div>

                <!-- Navigation -->
                <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
                    @php $user = auth()->user(); @endphp
                    
                    @if($user->isAdmin())
                        <p class="px-4 text-xs font-semibold text-white/70 uppercase tracking-wider mb-3">Administración</p>
                        <a href="{{ route('panel.config.critica') }}" class="nav-item {{ request()->routeIs('panel.config.critica') ? 'active' : '' }}">
                            <svg class="stroke-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span class="text-white font-semibold">Configuración crítica</span>
                        </a>
                        <a href="{{ route('panel.backups') }}" class="nav-item {{ request()->routeIs('panel.backups*') ? 'active' : '' }}">
                            <svg class="stroke-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/></svg>
                            <span class="text-white font-semibold">Respaldos</span>
                        </a>
                    @endif
                    
                    @if($user->isGerente())
                        <a href="{{ route('panel.index') }}" class="nav-item {{ request()->routeIs('panel.index') ? 'active' : '' }}">
                            <svg class="stroke-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                            <span class="text-white font-semibold">Panel</span>
                        </a>
                        <a href="{{ route('panel.dashboard') }}" class="nav-item {{ request()->routeIs('panel.dashboard') ? 'active' : '' }}">
                            <svg class="stroke-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                            <span class="text-white font-semibold">Dashboard</span>
                        </a>
                        <a href="{{ route('pos.index') }}" class="nav-item {{ request()->routeIs('pos.index') ? 'active' : '' }}">
                            <svg class="stroke-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            <span class="text-white font-semibold">Punto de Venta</span>
                        </a>

                        <p class="nav-section-title mt-4">Gestión</p>
                        <a href="{{ route('products.index') }}" class="nav-item {{ request()->routeIs('products.*') ? 'active' : '' }}">
                            <svg class="stroke-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            <span class="text-white font-semibold">Productos</span>
                        </a>
                        <a href="{{ route('vendedores.index') }}" class="nav-item {{ request()->routeIs('vendedores.*') ? 'active' : '' }}">
                            <svg class="stroke-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            <span class="text-white font-semibold">Vendedores</span>
                        </a>
                        <a href="{{ route('panel.caja.index') }}" class="nav-item {{ request()->routeIs('panel.caja.*') ? 'active' : '' }}">
                            <svg class="stroke-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            <span class="text-white font-semibold">Caja</span>
                        </a>

                        <p class="nav-section-title mt-4">Reportes</p>
                        <a href="{{ route('reportes.index') }}" class="nav-item {{ request()->routeIs('reportes.index') ? 'active' : '' }}">
                            <svg class="stroke-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <span class="text-white font-semibold">Reportes</span>
                        </a>
                        <a href="{{ route('panel.reportes.vendedores') }}" class="nav-item {{ request()->routeIs('panel.reportes.vendedores') ? 'active' : '' }}">
                            <svg class="stroke-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <span class="text-white font-semibold">Por vendedor</span>
                        </a>
                        <a href="{{ route('panel.bitacora') }}" class="nav-item {{ request()->routeIs('panel.bitacora') ? 'active' : '' }}">
                            <svg class="stroke-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <span class="text-white font-semibold">Bitácora</span>
                        </a>

                        <p class="nav-section-title mt-4">Herramientas</p>
                        <a href="{{ route('ia.index') }}" class="nav-item {{ request()->routeIs('ia.index') ? 'active' : '' }}">
                            <svg class="stroke-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                            <span class="text-white font-semibold">Asistente IA</span>
                        </a>
                        <a href="{{ route('panel.clima') }}" class="nav-item {{ request()->routeIs('panel.clima') ? 'active' : '' }}">
                            <svg class="stroke-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/></svg>
                            <span class="text-white font-semibold">Clima</span>
                        </a>
                        <a href="{{ route('panel.config') }}" class="nav-item {{ request()->routeIs('panel.config') ? 'active' : '' }}">
                            <svg class="stroke-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span class="text-white font-semibold">Configuración</span>
                        </a>
                    @endif

                    @if($user->isVendedor())
                        <p class="px-4 text-xs font-semibold text-white/70 uppercase tracking-wider mb-3">Ventas</p>
                        <a href="{{ route('pos.index') }}" class="nav-item {{ request()->routeIs('pos.index') ? 'active' : '' }}">
                            <svg class="stroke-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            <span class="text-white font-semibold">Punto de Venta</span>
                        </a>
                    @endif
                </nav>

                <!-- User section -->
                <div class="px-4 py-4 border-t border-white/10">
                    <div class="flex items-center gap-3 px-3 py-2">
                        <div class="w-9 h-9 rounded-full bg-white/20 flex items-center justify-center text-white font-semibold text-sm">
                            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-white truncate">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-white/70 truncate">{{ ucfirst(auth()->user()->role) }}</p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="mt-2">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-white/80 hover:bg-white/10 hover:text-white transition-all">
                            <svg class="w-5 h-5 stroke-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            Cerrar sesión
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Overlay para móvil -->
        <div id="sidebar-overlay" class="fixed inset-0 bg-black/50 z-40 lg:hidden hidden" onclick="toggleSidebar()"></div>
        @endauth

        <!-- Main content -->
        <div class="flex-1 flex flex-col @auth lg:ml-64 @endauth">
            <!-- Header -->
            <header class="sticky top-0 z-30 bg-white border-b border-slate-200 shadow-sm">
                <div class="flex items-center justify-between h-16 px-4 sm:px-6">
                    <div class="flex items-center gap-4">
                        @auth
                        <!-- Toggle sidebar (móvil) -->
                        <button onclick="toggleSidebar()" class="lg:hidden p-2 rounded-lg hover:bg-slate-100 transition">
                            <svg class="w-6 h-6 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>
                        @endauth
                        
                        <!-- Logo para guest -->
                        @guest
                        <a href="{{ url('/') }}" class="flex items-center gap-2">
                            <img src="{{ asset('images/logo-creamyx.png') }}" alt="Creamyx" class="w-10 h-10 rounded-xl object-contain">
                            <span class="font-bold text-slate-800">Creamyx</span>
                        </a>
                        @endguest
                        
                        @auth
                        <h2 class="text-lg font-semibold text-slate-800 hidden sm:block">@yield('title', 'Dashboard')</h2>
                        @endauth
                    </div>

                    <div class="flex items-center gap-3">
                        @guest
                        <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-medium text-white accent-gradient rounded-lg hover:opacity-90 transition">
                            Iniciar sesión
                        </a>
                        @endguest
                        
                        @auth
                        <!-- Info rápida -->
                        <div class="hidden sm:flex items-center gap-2 text-sm text-slate-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            {{ now()->format('d M, Y') }}
                        </div>
                        @endauth
                    </div>
                </div>
            </header>

            <!-- Content -->
            <main class="flex-1 p-4 sm:p-6 lg:p-8">
                <div class="max-w-7xl mx-auto">
                    @yield('content')
                </div>
            </main>

            <!-- Footer -->
            <footer class="border-t border-slate-200 bg-white">
                <div class="px-6 py-4 flex flex-col sm:flex-row items-center justify-center gap-2 text-sm text-slate-500">
                    <span>© {{ date('Y') }} Creamyx</span>
                    <span class="hidden sm:inline">—</span>
                    <span class="flex items-center gap-2">
                        Desarrollado por 
                        <img src="{{ asset('images/logo-smartcore.png') }}" alt="SmartCore Solutions" class="h-5 w-auto inline-block">
                        <span class="font-medium text-slate-600">SmartCore Solutions</span>
                    </span>
                </div>
            </footer>
        </div>
    </div>

    @auth
    @include('partials.chat-flotante')
    @endauth
    
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }
    </script>
    
    @stack('scripts')
</body>
</html>
