@extends('layouts.app')

@section('title', 'Productos')

@section('page_title', 'Productos 📦')
@section('page_subtitle', 'Aquí aparecerá tu catálogo. Por ahora mostramos un diseño de ejemplo.')

@section('content')
    <div class="flex items-center justify-between gap-3">
        <div class="text-sm text-slate-600">
            Total: <span class="font-semibold">3</span> productos (demo)
        </div>

        <button class="px-4 py-2 rounded-xl bg-pink-600 text-white font-semibold hover:bg-pink-700 transition shadow-sm">
            + Nuevo producto
        </button>
    </div>

    <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @php
            $items = [
                ['name' => 'Paleta de limón', 'price' => '$20.00', 'stock' => '25', 'tag' => 'Cítrica 🍋'],
                ['name' => 'Paleta de fresa', 'price' => '$22.00', 'stock' => '18', 'tag' => 'Clásica 🍓'],
                ['name' => 'Paleta de chocolate', 'price' => '$25.00', 'stock' => '12', 'tag' => 'Creamy 🍫'],
            ];
        @endphp

        @foreach($items as $p)
            <div class="rounded-3xl bg-white border border-slate-200 p-5 shadow-sm hover:shadow-md transition">
                <div class="flex items-start justify-between">
                    <div class="h-11 w-11 rounded-2xl bg-gradient-to-br from-pink-100 to-amber-100 grid place-items-center">
                        <span class="text-lg">🍦</span>
                    </div>
                    <span class="text-xs font-semibold px-2 py-1 rounded-full bg-slate-100 text-slate-600">
                        {{ $p['tag'] }}
                    </span>
                </div>

                <h3 class="mt-4 font-extrabold text-lg">{{ $p['name'] }}</h3>
                <div class="mt-2 flex items-center justify-between text-sm">
                    <span class="text-slate-600">Precio</span>
                    <span class="font-semibold">{{ $p['price'] }}</span>
                </div>
                <div class="mt-1 flex items-center justify-between text-sm">
                    <span class="text-slate-600">Stock</span>
                    <span class="font-semibold">{{ $p['stock'] }}</span>
                </div>

                <div class="mt-4 flex gap-2">
                    <button class="flex-1 px-3 py-2 rounded-xl bg-slate-900 text-white font-semibold hover:bg-slate-800 transition">
                        Editar
                    </button>
                    <button class="px-3 py-2 rounded-xl bg-slate-100 text-slate-700 font-semibold hover:bg-slate-200 transition">
                        Eliminar
                    </button>
                </div>
            </div>
        @endforeach
    </div>
@endsection
