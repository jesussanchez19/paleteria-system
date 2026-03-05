<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Iniciar Sesión - Paletería</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen flex">
        <!-- Panel izquierdo - Decorativo -->
        <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-pink-400 via-pink-500 to-rose-500 relative overflow-hidden">
            <!-- Círculos decorativos -->
            <div class="absolute top-20 left-20 w-32 h-32 bg-white/20 rounded-full blur-xl"></div>
            <div class="absolute bottom-32 right-20 w-48 h-48 bg-yellow-300/30 rounded-full blur-2xl"></div>
            <div class="absolute top-1/2 left-10 w-24 h-24 bg-white/20 rounded-full blur-xl"></div>
            <div class="absolute bottom-20 left-1/3 w-20 h-20 bg-rose-300/30 rounded-full blur-lg"></div>
            
            <!-- Contenido del panel -->
            <div class="relative z-10 flex flex-col justify-center items-center w-full p-12 text-white">
                <div class="text-8xl mb-6">🍦</div>
                <h1 class="text-4xl font-extrabold mb-4 text-center">Paletería System</h1>
                <p class="text-xl text-white/90 text-center max-w-md">
                    Sistema de gestión para tu negocio de paletas y helados
                </p>
                
                <!-- Iconos decorativos -->
                <div class="flex gap-4 mt-12 text-4xl">
                    <span class="animate-bounce" style="animation-delay: 0s;">🧊</span>
                    <span class="animate-bounce" style="animation-delay: 0.1s;">🍨</span>
                    <span class="animate-bounce" style="animation-delay: 0.2s;">🍧</span>
                    <span class="animate-bounce" style="animation-delay: 0.3s;">🥤</span>
                </div>
            </div>
            
            <!-- Onda decorativa -->
            <svg class="absolute bottom-0 left-0 w-full" viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M0 120L60 105C120 90 240 60 360 45C480 30 600 30 720 37.5C840 45 960 60 1080 67.5C1200 75 1320 75 1380 75L1440 75V120H1380C1320 120 1200 120 1080 120C960 120 840 120 720 120C600 120 480 120 360 120C240 120 120 120 60 120H0Z" fill="white" fill-opacity="0.1"/>
            </svg>
        </div>

        <!-- Panel derecho - Formulario -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-8 bg-slate-50">
            <div class="w-full max-w-md">
                <!-- Logo móvil -->
                <div class="lg:hidden text-center mb-8">
                    <span class="text-6xl">🍦</span>
                    <h1 class="text-2xl font-extrabold text-slate-800 mt-2">Paletería System</h1>
                </div>

                <!-- Card del formulario -->
                <div class="bg-white rounded-3xl shadow-xl p-8 border border-slate-100">
                    <div class="text-center mb-8">
                        <h2 class="text-2xl font-extrabold text-slate-800">¡Bienvenido! 👋</h2>
                        <p class="text-slate-500 mt-2">Ingresa tus credenciales para continuar</p>
                    </div>

                    <!-- Session Status -->
                    @if (session('status'))
                        <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-medium">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}" class="space-y-5">
                        @csrf

                        <!-- Email Address -->
                        <div>
                            <label for="email" class="block text-sm font-bold text-slate-700 mb-2">
                                📧 Correo electrónico
                            </label>
                            <input id="email" 
                                   type="email" 
                                   name="email" 
                                   value="{{ old('email') }}" 
                                   required 
                                   autofocus 
                                   autocomplete="username"
                                   class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-pink-500 focus:ring-2 focus:ring-pink-200 transition-all outline-none text-slate-800 placeholder-slate-400"
                                   placeholder="tu@email.com">
                            @error('email')
                                <p class="mt-2 text-sm text-rose-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div>
                            <label for="password" class="block text-sm font-bold text-slate-700 mb-2">
                                🔒 Contraseña
                            </label>
                            <input id="password" 
                                   type="password" 
                                   name="password" 
                                   required 
                                   autocomplete="current-password"
                                   class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-pink-500 focus:ring-2 focus:ring-pink-200 transition-all outline-none text-slate-800 placeholder-slate-400"
                                   placeholder="••••••••">
                            @error('password')
                                <p class="mt-2 text-sm text-rose-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Remember Me -->
                        <div class="flex items-center justify-between">
                            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                                <input id="remember_me" 
                                       type="checkbox" 
                                       name="remember"
                                       class="w-4 h-4 rounded border-slate-300 text-pink-500 focus:ring-pink-200 transition">
                                <span class="ml-2 text-sm text-slate-600">Recordarme</span>
                            </label>

                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" 
                                   class="text-sm font-medium text-pink-600 hover:text-pink-700 transition">
                                    ¿Olvidaste tu contraseña?
                                </a>
                            @endif
                        </div>

                        <!-- Submit Button -->
                        <button type="submit"
                                class="w-full py-3 px-4 bg-gradient-to-r from-pink-500 to-rose-500 text-white font-extrabold rounded-xl hover:from-pink-600 hover:to-rose-600 focus:ring-4 focus:ring-pink-200 transition-all transform hover:scale-[1.02] active:scale-[0.98] shadow-lg shadow-pink-500/30">
                            Iniciar Sesión →
                        </button>
                    </form>

                </div>

                <!-- Footer -->
                <p class="text-center text-slate-400 text-sm mt-6">
                    © {{ date('Y') }} Paletería System. Todos los derechos reservados.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
