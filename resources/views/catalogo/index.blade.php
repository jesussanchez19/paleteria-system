@extends('layouts.app')

@section('title', 'Catálogo')

@section('content')
    <div class="flex items-end justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-extrabold">Catálogo 🍦</h1>
            <p class="text-slate-600">Solo productos disponibles.</p>
        </div>
        <span class="text-sm text-slate-600">
            Mostrando: <b>{{ $products->count() }}</b>
        </span>
    </div>

    @if($products->isEmpty())
        <div class="bg-white border border-slate-200 rounded-2xl p-6 text-center">
            <p class="font-semibold">No hay productos disponibles por el momento.</p>
            <p class="text-slate-600 text-sm mt-1">Vuelve más tarde 😊</p>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            @foreach($products as $p)
                <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm hover:shadow-lg transition group">
                    {{-- Imagen del producto --}}
                    <div class="aspect-square bg-gradient-to-br from-pink-50 to-orange-50 relative overflow-hidden">
                        @if($p->image_url)
                            <img src="{{ $p->image_url }}" 
                                 alt="{{ $p->name }}" 
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <span class="text-6xl opacity-50">
                                    @switch($p->category)
                                        @case('Paleta')
                                        @case('Paletas')
                                            🍭
                                            @break
                                        @case('Helado')
                                        @case('Helados')
                                            🍦
                                            @break
                                        @case('Agua')
                                        @case('Aguas')
                                            🥤
                                            @break
                                        @case('Nieve')
                                        @case('Nieves')
                                            🍧
                                            @break
                                        @default
                                            🍨
                                    @endswitch
                                </span>
                            </div>
                        @endif
                        
                        {{-- Badge de categoría --}}
                        <span class="absolute top-3 right-3 text-xs font-semibold px-2 py-1 rounded-full bg-white/90 text-pink-700 shadow-sm">
                            {{ $p->category ?? 'Producto' }}
                        </span>
                    </div>
                    
                    {{-- Info del producto --}}
                    <div class="p-4">
                        <h2 class="font-bold text-lg leading-tight text-slate-800 mb-1">{{ $p->name }}</h2>
                        
                        @if($p->description)
                            <p class="text-sm text-slate-500 line-clamp-2 mb-3">{{ $p->description }}</p>
                        @endif

                        <div class="flex items-center justify-between">
                            <p class="text-2xl font-extrabold text-pink-600">
                                ${{ number_format($p->price, 2) }}
                            </p>

                            <p class="text-sm text-slate-500">
                                <span class="inline-flex items-center gap-1">
                                    📦 <b>{{ $p->stock }}</b>
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection
