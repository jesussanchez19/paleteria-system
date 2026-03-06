<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $business['name'] }} - {{ $business['slogan'] }}</title>
    <meta name="description" content="{{ $business['name'] }} - Los mejores helados, paletas y nieves de {{ $business['city'] }}. {{ $business['slogan'] }}">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        .hero-gradient {
            background: linear-gradient(135deg, #ec4899 0%, #f97316 50%, #fbbf24 100%);
        }
        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }
        .text-gradient {
            background: linear-gradient(135deg, #ec4899, #f97316);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .blob {
            border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%;
            animation: blob 8s ease-in-out infinite;
        }
        @keyframes blob {
            0%, 100% { border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%; }
            50% { border-radius: 70% 30% 30% 70% / 70% 70% 30% 30%; }
        }
        .float {
            animation: float 3s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
    </style>
</head>
<body class="bg-gradient-to-br from-orange-50 via-pink-50 to-yellow-50 min-h-screen">

    {{-- Navbar --}}
    <nav class="fixed top-0 left-0 right-0 z-50 bg-white/80 backdrop-blur-lg border-b border-pink-100 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <a href="#inicio" class="flex items-center gap-2">
                    <span class="text-3xl">🍦</span>
                    <span class="text-xl font-extrabold text-gradient">{{ $business['name'] }}</span>
                </a>
                
                <div class="hidden md:flex items-center gap-8">
                    <a href="#inicio" class="text-sm font-semibold text-slate-700 hover:text-pink-600 transition">Inicio</a>
                    <a href="#productos" class="text-sm font-semibold text-slate-700 hover:text-pink-600 transition">Productos</a>
                    <a href="#nosotros" class="text-sm font-semibold text-slate-700 hover:text-pink-600 transition">Nosotros</a>
                    <a href="#contacto" class="text-sm font-semibold text-slate-700 hover:text-pink-600 transition">Contacto</a>
                    <a href="{{ route('login') }}" class="px-4 py-2 bg-pink-600 text-white text-sm font-bold rounded-full hover:bg-pink-700 transition shadow-lg shadow-pink-200">
                        Ingresar
                    </a>
                </div>

                {{-- Mobile menu button --}}
                <button id="mobile-menu-btn" class="md:hidden p-2 rounded-lg hover:bg-pink-50">
                    <svg class="w-6 h-6 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Mobile menu --}}
        <div id="mobile-menu" class="hidden md:hidden bg-white border-t border-pink-100">
            <div class="px-4 py-4 space-y-3">
                <a href="#inicio" class="block text-sm font-semibold text-slate-700 hover:text-pink-600">Inicio</a>
                <a href="#productos" class="block text-sm font-semibold text-slate-700 hover:text-pink-600">Productos</a>
                <a href="#nosotros" class="block text-sm font-semibold text-slate-700 hover:text-pink-600">Nosotros</a>
                <a href="#contacto" class="block text-sm font-semibold text-slate-700 hover:text-pink-600">Contacto</a>
                <a href="{{ route('login') }}" class="block w-full text-center px-4 py-2 bg-pink-600 text-white text-sm font-bold rounded-full">
                    Ingresar
                </a>
            </div>
        </div>
    </nav>

    {{-- Hero Section --}}
    <section id="inicio" class="relative min-h-screen flex items-center pt-16 overflow-hidden">
        {{-- Background blobs --}}
        <div class="absolute top-20 left-10 w-72 h-72 bg-pink-300/30 rounded-full blur-3xl blob"></div>
        <div class="absolute bottom-20 right-10 w-96 h-96 bg-orange-300/30 rounded-full blur-3xl blob" style="animation-delay: 2s;"></div>
        <div class="absolute top-1/2 left-1/2 w-64 h-64 bg-yellow-300/20 rounded-full blur-3xl blob" style="animation-delay: 4s;"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div class="text-center lg:text-left">
                    <span class="inline-block px-4 py-2 bg-pink-100 text-pink-700 text-sm font-bold rounded-full mb-6">
                        🎉 ¡Bienvenidos!
                    </span>
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black text-slate-900 leading-tight mb-6">
                        Los mejores
                        <span class="text-gradient">helados</span>
                        de {{ $business['city'] }}
                    </h1>
                    <p class="text-lg sm:text-xl text-slate-600 mb-8 max-w-lg mx-auto lg:mx-0">
                        {{ $business['slogan'] }} Descubre nuestra amplia variedad de paletas, helados, nieves y más.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                        <a href="#productos" class="px-8 py-4 bg-gradient-to-r from-pink-600 to-orange-500 text-white font-bold rounded-full shadow-lg shadow-pink-200 hover:shadow-xl hover:scale-105 transition-all">
                            Ver menú 🍨
                        </a>
                        <a href="#contacto" class="px-8 py-4 bg-white text-pink-600 font-bold rounded-full border-2 border-pink-200 hover:border-pink-400 hover:bg-pink-50 transition-all">
                            Contáctanos 📍
                        </a>
                    </div>
                </div>

                <div class="relative flex justify-center">
                    <div class="relative w-80 h-80 lg:w-96 lg:h-96">
                        {{-- Decorative circle --}}
                        <div class="absolute inset-0 hero-gradient rounded-full opacity-20 blur-2xl"></div>
                        <div class="absolute inset-4 bg-gradient-to-br from-pink-400 to-orange-400 rounded-full flex items-center justify-center shadow-2xl">
                            <span class="text-[160px] lg:text-[200px] float">🍦</span>
                        </div>
                        {{-- Floating elements --}}
                        <span class="absolute top-0 right-0 text-5xl float" style="animation-delay: 0.5s;">🍓</span>
                        <span class="absolute bottom-10 left-0 text-4xl float" style="animation-delay: 1s;">🍫</span>
                        <span class="absolute top-1/2 right-0 text-3xl float" style="animation-delay: 1.5s;">🥝</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Wave divider --}}
        <div class="absolute bottom-0 left-0 right-0">
            <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M0 120L60 105C120 90 240 60 360 45C480 30 600 30 720 37.5C840 45 960 60 1080 67.5C1200 75 1320 75 1380 75L1440 75V120H1380C1320 120 1200 120 1080 120C960 120 840 120 720 120C600 120 480 120 360 120C240 120 120 120 60 120H0Z" fill="white"/>
            </svg>
        </div>
    </section>

    {{-- Stats Section --}}
    <section class="bg-white py-12 relative z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                <div>
                    <p class="text-4xl font-black text-gradient">{{ $products->count() }}+</p>
                    <p class="text-slate-600 font-semibold mt-1">Sabores</p>
                </div>
                <div>
                    <p class="text-4xl font-black text-gradient">{{ $categories->count() }}</p>
                    <p class="text-slate-600 font-semibold mt-1">Categorías</p>
                </div>
                <div>
                    <p class="text-4xl font-black text-gradient">100%</p>
                    <p class="text-slate-600 font-semibold mt-1">Natural</p>
                </div>
                <div>
                    <p class="text-4xl font-black text-gradient">❤️</p>
                    <p class="text-slate-600 font-semibold mt-1">Hecho con amor</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Featured Products --}}
    @if($featured->count() > 0)
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <span class="text-sm font-bold text-pink-600 uppercase tracking-wider">⭐ Destacados</span>
                <h2 class="text-3xl sm:text-4xl font-black text-slate-900 mt-2">Nuestros favoritos</h2>
                <p class="text-slate-600 mt-3 max-w-2xl mx-auto">Los productos más populares que nuestros clientes adoran</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($featured as $p)
                <div class="bg-gradient-to-br from-pink-50 to-orange-50 rounded-3xl p-6 card-hover border border-pink-100">
                    <div class="aspect-square bg-white rounded-2xl flex items-center justify-center mb-4 shadow-inner">
                        @if($p->image_url)
                            <img src="{{ $p->image_url }}" alt="{{ $p->name }}" class="w-full h-full object-cover rounded-2xl">
                        @else
                            <span class="text-7xl">
                                @switch($p->category)
                                    @case('Paleta') @case('Paletas') 🍭 @break
                                    @case('Helado') @case('Helados') 🍦 @break
                                    @case('Agua') @case('Aguas') 🥤 @break
                                    @case('Bolis') 🧊 @break
                                    @default 🍨
                                @endswitch
                            </span>
                        @endif
                    </div>
                    <span class="inline-block px-3 py-1 bg-pink-100 text-pink-700 text-xs font-bold rounded-full mb-2">
                        {{ $p->category ?? 'Producto' }}
                    </span>
                    <h3 class="text-lg font-bold text-slate-800">{{ $p->name }}</h3>
                    @if($p->description)
                        <p class="text-sm text-slate-500 mt-1 line-clamp-2">{{ $p->description }}</p>
                    @endif
                    <p class="text-2xl font-black text-gradient mt-3">${{ number_format($p->price, 2) }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- All Products by Category --}}
    <section id="productos" class="py-20 bg-gradient-to-br from-orange-50 via-pink-50 to-yellow-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <span class="text-sm font-bold text-pink-600 uppercase tracking-wider">🍦 Nuestro Menú</span>
                <h2 class="text-3xl sm:text-4xl font-black text-slate-900 mt-2">Todos nuestros productos</h2>
                <p class="text-slate-600 mt-3 max-w-2xl mx-auto">Explora nuestra deliciosa variedad de sabores y encuentra tu favorito</p>
            </div>

            {{-- Category tabs --}}
            <div class="flex flex-wrap justify-center gap-3 mb-10">
                <button onclick="filterCategory('all')" data-category="all" class="category-btn px-6 py-2 bg-pink-600 text-white font-bold rounded-full shadow-lg transition-all">
                    Todos
                </button>
                @foreach($categories as $cat)
                <button onclick="filterCategory('{{ Str::slug($cat) }}')" data-category="{{ Str::slug($cat) }}" class="category-btn px-6 py-2 bg-white text-slate-700 font-bold rounded-full border border-pink-200 hover:border-pink-400 hover:bg-pink-50 transition-all">
                    @switch($cat)
                        @case('Paleta') @case('Paletas') 🍭 @break
                        @case('Helado') @case('Helados') 🍦 @break
                        @case('Agua') @case('Aguas') 🥤 @break
                        @case('Bolis') 🧊 @break
                        @default 🍨
                    @endswitch
                    {{ $cat }}
                </button>
                @endforeach
            </div>

            {{-- Products grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6" id="products-grid">
                @foreach($products as $p)
                <div class="product-card bg-white rounded-3xl overflow-hidden shadow-lg card-hover" data-category="{{ Str::slug($p->category) }}">
                    <div class="aspect-square bg-gradient-to-br from-pink-50 to-orange-50 flex items-center justify-center relative overflow-hidden">
                        @if($p->image_url)
                            <img src="{{ $p->image_url }}" alt="{{ $p->name }}" class="w-full h-full object-cover">
                        @else
                            <span class="text-8xl opacity-80">
                                @switch($p->category)
                                    @case('Paleta') @case('Paletas') 🍭 @break
                                    @case('Helado') @case('Helados') 🍦 @break
                                    @case('Agua') @case('Aguas') 🥤 @break
                                    @case('Bolis') 🧊 @break
                                    @default 🍨
                                @endswitch
                            </span>
                        @endif
                        <span class="absolute top-4 right-4 px-3 py-1 bg-white/90 text-pink-700 text-xs font-bold rounded-full shadow-sm">
                            {{ $p->category ?? 'Producto' }}
                        </span>
                    </div>
                    <div class="p-5">
                        <h3 class="text-lg font-bold text-slate-800">{{ $p->name }}</h3>
                        @if($p->description)
                            <p class="text-sm text-slate-500 mt-1 line-clamp-2">{{ $p->description }}</p>
                        @endif
                        <div class="flex items-center justify-between mt-4">
                            <p class="text-2xl font-black text-gradient">${{ number_format($p->price, 2) }}</p>
                            <span class="text-sm text-slate-400">📦 {{ $p->stock }}</span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            @if($products->isEmpty())
            <div class="text-center py-16">
                <span class="text-6xl">🍦</span>
                <p class="text-xl font-bold text-slate-700 mt-4">No hay productos disponibles</p>
                <p class="text-slate-500 mt-2">Vuelve pronto, estamos preparando cosas deliciosas</p>
            </div>
            @endif
        </div>
    </section>

    {{-- About Section --}}
    <section id="nosotros" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div class="relative">
                    <div class="absolute inset-0 bg-gradient-to-br from-pink-400 to-orange-400 rounded-3xl transform rotate-3"></div>
                    <div class="relative bg-gradient-to-br from-pink-100 to-orange-100 rounded-3xl p-8 flex items-center justify-center min-h-[400px]">
                        <div class="text-center">
                            <span class="text-[120px]">🏪</span>
                            <p class="text-2xl font-bold text-slate-700 mt-4">{{ $business['name'] }}</p>
                        </div>
                    </div>
                </div>

                <div>
                    <span class="text-sm font-bold text-pink-600 uppercase tracking-wider">💝 Nuestra Historia</span>
                    <h2 class="text-3xl sm:text-4xl font-black text-slate-900 mt-2 mb-6">Tradición familiar desde hace años</h2>
                    <div class="space-y-4 text-slate-600">
                        <p>
                            En <strong class="text-slate-800">{{ $business['name'] }}</strong>, nos dedicamos a crear los helados y paletas más deliciosos de {{ $business['city'] }}, usando recetas tradicionales y los ingredientes más frescos.
                        </p>
                        <p>
                            Cada uno de nuestros productos está hecho con amor y dedicación, manteniendo la calidad artesanal que nos caracteriza. Desde nuestras cremosas nieves hasta nuestras refrescantes paletas de agua, todo es preparado diariamente para garantizar la máxima frescura.
                        </p>
                        <p>
                            ¡Te invitamos a visitarnos y descubrir por qué somos los favoritos de la comunidad! 🎉
                        </p>
                    </div>

                    <div class="grid grid-cols-2 gap-6 mt-8">
                        <div class="bg-pink-50 rounded-2xl p-4 text-center">
                            <span class="text-3xl">🥛</span>
                            <p class="font-bold text-slate-800 mt-2">100% Natural</p>
                            <p class="text-sm text-slate-500">Sin conservadores</p>
                        </div>
                        <div class="bg-orange-50 rounded-2xl p-4 text-center">
                            <span class="text-3xl">🍓</span>
                            <p class="font-bold text-slate-800 mt-2">Fruta Fresca</p>
                            <p class="text-sm text-slate-500">Todos los días</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Contact Section --}}
    <section id="contacto" class="py-20 bg-gradient-to-br from-pink-600 to-orange-500 text-white relative overflow-hidden">
        {{-- Background decorations --}}
        <div class="absolute top-10 left-10 w-40 h-40 bg-white/10 rounded-full blur-2xl"></div>
        <div class="absolute bottom-10 right-10 w-60 h-60 bg-white/10 rounded-full blur-3xl"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <span class="text-sm font-bold text-pink-200 uppercase tracking-wider">📍 Encuéntranos</span>
                <h2 class="text-3xl sm:text-4xl font-black mt-2">¡Ven a visitarnos!</h2>
                <p class="text-pink-100 mt-3">Estamos esperándote con los brazos abiertos</p>
            </div>

            {{-- Map Section --}}
            @if($business['lat'] && $business['lng'])
            <div class="mb-12">
                <a href="https://www.google.com/maps/dir/?api=1&destination={{ $business['lat'] }},{{ $business['lng'] }}" 
                   target="_blank"
                   class="block relative group">
                    <div class="bg-white rounded-3xl overflow-hidden shadow-2xl">
                        {{-- Map embed usando OpenStreetMap --}}
                        <iframe 
                            src="https://www.openstreetmap.org/export/embed.html?bbox={{ $business['lng'] - 0.01 }}%2C{{ $business['lat'] - 0.008 }}%2C{{ $business['lng'] + 0.01 }}%2C{{ $business['lat'] + 0.008 }}&layer=mapnik&marker={{ $business['lat'] }}%2C{{ $business['lng'] }}"
                            class="w-full h-64 md:h-80 pointer-events-none"
                            style="border: 0;"
                            loading="lazy">
                        </iframe>
                        {{-- Overlay con CTA --}}
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent flex items-end justify-center pb-6 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            <span class="px-6 py-3 bg-white text-pink-600 font-bold rounded-full shadow-lg flex items-center gap-2">
                                🗺️ Abrir en Google Maps
                            </span>
                        </div>
                    </div>
                    {{-- Click hint --}}
                    <div class="text-center mt-4">
                        <span class="inline-flex items-center gap-2 px-4 py-2 bg-white/20 rounded-full text-sm font-semibold">
                            👆 Toca el mapa para obtener indicaciones
                        </span>
                    </div>
                </a>
            </div>
            @endif

            <div class="grid md:grid-cols-3 gap-8 text-center">
                {{-- Ubicación con link a Maps --}}
                <a href="https://www.google.com/maps/dir/?api=1&destination={{ $business['lat'] }},{{ $business['lng'] }}" 
                   target="_blank"
                   class="bg-white/10 backdrop-blur-sm rounded-3xl p-8 hover:bg-white/20 transition-all cursor-pointer group">
                    <span class="text-5xl group-hover:scale-110 inline-block transition-transform">📍</span>
                    <h3 class="text-xl font-bold mt-4">Ubicación</h3>
                    <p class="text-pink-100 mt-2">
                        @if($business['address'])
                            {{ $business['address'] }}<br>
                        @endif
                        {{ $business['city'] }}
                    </p>
                    <span class="inline-flex items-center gap-1 mt-4 text-sm font-bold text-white/80 group-hover:text-white">
                        🗺️ Cómo llegar →
                    </span>
                </a>

                <div class="bg-white/10 backdrop-blur-sm rounded-3xl p-8">
                    <span class="text-5xl">🕐</span>
                    <h3 class="text-xl font-bold mt-4">Horario</h3>
                    <p class="text-pink-100 mt-2">
                        Lunes a Domingo<br>
                        <span class="text-2xl font-bold">{{ $business['open_time'] }} - {{ $business['close_time'] }}</span>
                    </p>
                </div>

                <div class="bg-white/10 backdrop-blur-sm rounded-3xl p-8">
                    <span class="text-5xl">📞</span>
                    <h3 class="text-xl font-bold mt-4">Contacto</h3>
                    <p class="text-pink-100 mt-2">
                        @if($business['phone'])
                            <a href="tel:{{ $business['phone'] }}" class="hover:text-white transition">{{ $business['phone'] }}</a>
                        @else
                            Visítanos en tienda
                        @endif
                    </p>
                    @if($business['whatsapp'])
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $business['whatsapp']) }}" 
                           class="inline-flex items-center gap-2 mt-4 px-4 py-2 bg-green-500 hover:bg-green-600 rounded-full text-sm font-bold transition" target="_blank">
                            💬 WhatsApp
                        </a>
                    @endif
                </div>
            </div>

            {{-- Social links --}}
            @if($business['facebook'] || $business['instagram'])
            <div class="flex justify-center gap-6 mt-12">
                @if($business['facebook'])
                <a href="{{ $business['facebook'] }}" target="_blank" class="w-14 h-14 bg-white/20 hover:bg-white/30 rounded-full flex items-center justify-center text-2xl transition">
                    📘
                </a>
                @endif
                @if($business['instagram'])
                <a href="{{ $business['instagram'] }}" target="_blank" class="w-14 h-14 bg-white/20 hover:bg-white/30 rounded-full flex items-center justify-center text-2xl transition">
                    📸
                </a>
                @endif
            </div>
            @endif
        </div>
    </section>

    {{-- Footer --}}
    <footer class="bg-slate-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-3 gap-8">
                <div>
                    <div class="flex items-center gap-2 mb-4">
                        <span class="text-3xl">🍦</span>
                        <span class="text-xl font-extrabold">{{ $business['name'] }}</span>
                    </div>
                    <p class="text-slate-400">{{ $business['slogan'] }}</p>
                </div>

                <div>
                    <h4 class="font-bold mb-4">Enlaces</h4>
                    <ul class="space-y-2 text-slate-400">
                        <li><a href="#inicio" class="hover:text-white transition">Inicio</a></li>
                        <li><a href="#productos" class="hover:text-white transition">Productos</a></li>
                        <li><a href="#nosotros" class="hover:text-white transition">Nosotros</a></li>
                        <li><a href="#contacto" class="hover:text-white transition">Contacto</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-bold mb-4">Horario</h4>
                    <p class="text-slate-400">
                        Abierto todos los días<br>
                        {{ $business['open_time'] }} - {{ $business['close_time'] }}
                    </p>
                    @if($business['phone'])
                        <p class="text-slate-400 mt-4">
                            📞 {{ $business['phone'] }}
                        </p>
                    @endif
                </div>
            </div>

            <div class="border-t border-slate-800 mt-10 pt-8 text-center text-slate-500 text-sm">
                <p>&copy; {{ date('Y') }} {{ $business['name'] }}. Todos los derechos reservados.</p>
                <p class="mt-2 text-slate-600">Desarrollado por <span class="font-semibold text-pink-400">SmartCore Solutions</span></p>
            </div>
        </div>
    </footer>

    <script>
        // Mobile menu toggle
        document.getElementById('mobile-menu-btn')?.addEventListener('click', function() {
            document.getElementById('mobile-menu').classList.toggle('hidden');
        });

        // Category filter
        function filterCategory(category) {
            const cards = document.querySelectorAll('.product-card');
            const buttons = document.querySelectorAll('.category-btn');

            buttons.forEach(btn => {
                if (btn.dataset.category === category) {
                    btn.classList.remove('bg-white', 'text-slate-700', 'border', 'border-pink-200');
                    btn.classList.add('bg-pink-600', 'text-white', 'shadow-lg');
                } else {
                    btn.classList.add('bg-white', 'text-slate-700', 'border', 'border-pink-200');
                    btn.classList.remove('bg-pink-600', 'text-white', 'shadow-lg');
                }
            });

            cards.forEach(card => {
                if (category === 'all' || card.dataset.category === category) {
                    card.style.display = 'block';
                    card.style.animation = 'fadeIn 0.3s ease';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        // Close mobile menu when clicking a link
        document.querySelectorAll('#mobile-menu a').forEach(link => {
            link.addEventListener('click', () => {
                document.getElementById('mobile-menu').classList.add('hidden');
            });
        });

        // Smooth reveal on scroll
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, { threshold: 0.1 });

        document.querySelectorAll('section').forEach(section => {
            section.style.opacity = '0';
            section.style.transform = 'translateY(20px)';
            section.style.transition = 'all 0.6s ease';
            observer.observe(section);
        });

        // Fix initial section visibility
        document.querySelector('#inicio').style.opacity = '1';
        document.querySelector('#inicio').style.transform = 'translateY(0)';
    </script>

    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</body>
</html>
