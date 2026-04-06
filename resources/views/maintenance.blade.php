<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema en Mantenimiento - Creamyx</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        .float-animation {
            animation: float 3s ease-in-out infinite;
        }
        @keyframes pulse-bg {
            0%, 100% { opacity: 0.1; }
            50% { opacity: 0.2; }
        }
        .pulse-bg {
            animation: pulse-bg 4s ease-in-out infinite;
        }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900 flex items-center justify-center p-4 overflow-hidden">
    
    {{-- Decorative background elements --}}
    <div class="absolute inset-0 overflow-hidden">
        <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-purple-500 rounded-full filter blur-3xl pulse-bg"></div>
        <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-pink-500 rounded-full filter blur-3xl pulse-bg" style="animation-delay: 2s;"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-blue-500 rounded-full filter blur-3xl pulse-bg" style="animation-delay: 1s;"></div>
    </div>

    <div class="relative z-10 max-w-2xl w-full">
        {{-- Main card --}}
        <div class="bg-white/10 backdrop-blur-xl rounded-3xl p-8 md:p-12 shadow-2xl border border-white/20">
            
            {{-- Icon --}}
            <div class="flex justify-center mb-8">
                <div class="w-32 h-32 bg-gradient-to-br from-amber-400 to-orange-500 rounded-full flex items-center justify-center float-animation shadow-lg shadow-orange-500/30">
                    <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
            </div>

            {{-- Text content --}}
            <div class="text-center">
                <h1 class="text-3xl md:text-4xl font-bold text-white mb-4">
                    Sistema en Mantenimiento
                </h1>
                <p class="text-lg text-white/70 mb-8">
                    Estamos realizando mejoras para brindarte una mejor experiencia.
                    <br>
                    Por favor, vuelve a intentarlo en unos minutos.
                </p>

                {{-- Status indicator --}}
                <div class="inline-flex items-center gap-3 bg-white/10 rounded-full px-6 py-3 mb-8">
                    <span class="relative flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-amber-500"></span>
                    </span>
                    <span class="text-white/80 text-sm font-medium">En progreso</span>
                </div>

                {{-- Info cards --}}
                <div class="grid md:grid-cols-2 gap-4 mb-8">
                    <div class="bg-white/5 rounded-xl p-4 border border-white/10">
                        <div class="text-2xl mb-2">🍦</div>
                        <h3 class="text-white font-semibold mb-1">Creamyx</h3>
                        <p class="text-white/50 text-sm">Sistema de Punto de Venta</p>
                    </div>
                    <div class="bg-white/5 rounded-xl p-4 border border-white/10">
                        <div class="text-2xl mb-2">⏱️</div>
                        <h3 class="text-white font-semibold mb-1">Tiempo estimado</h3>
                        <p class="text-white/50 text-sm">Unos minutos</p>
                    </div>
                </div>

                {{-- Admin login link --}}
                <div class="pt-4 border-t border-white/10">
                    <p class="text-white/40 text-sm mb-3">¿Eres administrador?</p>
                    <a href="{{ route('login') }}" 
                       class="inline-flex items-center gap-2 text-white/60 hover:text-white transition-colors text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                        </svg>
                        Acceder al panel de administración
                    </a>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <p class="text-center text-white/30 text-sm mt-6">
            © {{ date('Y') }} Creamyx • Disculpa las molestias
        </p>
    </div>
</body>
</html>
