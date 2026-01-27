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
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($products as $p)
                <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm hover:shadow transition">
                    <div class="flex items-start justify-between gap-3">
                        <h2 class="font-bold text-lg leading-tight">{{ $p->name }}</h2>
                        <span class="text-xs font-semibold px-2 py-1 rounded-full bg-pink-50 text-pink-700 border border-pink-100">
                            {{ $p->category ?? 'Producto' }}
                        </span>
                    </div>

                    <div class="mt-4 flex items-center justify-between">
                        <p class="text-xl font-extrabold text-slate-900">
                            ${{ number_format($p->price, 2) }}
                        </p>

                        <p class="text-sm text-slate-600">
                            Stock: <b>{{ $p->stock }}</b>
                        </p>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection
