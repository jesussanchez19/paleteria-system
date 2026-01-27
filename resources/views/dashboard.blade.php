@extends('layouts.app')

@section('title', 'Dashboard')

@section('page_title', '¡Bienvenido! 🍧')
@section('page_subtitle', 'Elige una sección para comenzar. Todo en un solo lugar.')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        {{-- Card Productos --}}
        <a href="{{ route('products.index') }}"
           class="group rounded-3xl bg-white border border-slate-200 p-6 shadow-sm hover:shadow-md transition">
            <div class="flex items-start justify-between">
                <div class="h-12 w-12 rounded-2xl bg-pink-100 grid place-items-center">
                    <span class="text-xl">📦</span>
                </div>
                <span class="text-slate-400 group-hover:text-slate-600 transition">→</span>
            </div>
            <h3 class="mt-4 font-extrabold text-lg">Productos</h3>
            <p class="mt-1 text-slate-600">Administra el catálogo e inventario.</p>
            <div class="mt-4 text-sm font-semibold text-pink-700">Abrir módulo</div>
        </a>

        {{-- Card Ventas --}}
        <a href="{{ route('sales.index') }}"
           class="group rounded-3xl bg-white border border-slate-200 p-6 shadow-sm hover:shadow-md transition">
            <div class="flex items-start justify-between">
                <div class="h-12 w-12 rounded-2xl bg-sky-100 grid place-items-center">
                    <span class="text-xl">🧾</span>
                </div>
                <span class="text-slate-400 group-hover:text-slate-600 transition">→</span>
            </div>
            <h3 class="mt-4 font-extrabold text-lg">Ventas</h3>
            <p class="mt-1 text-slate-600">Revisa historial y totales.</p>
            <div class="mt-4 text-sm font-semibold text-sky-700">Abrir módulo</div>
        </a>

        {{-- Card Reporte (placeholder) --}}
        <div class="rounded-3xl bg-white border border-slate-200 p-6 shadow-sm">
            <div class="h-12 w-12 rounded-2xl bg-amber-100 grid place-items-center">
                <span class="text-xl">📄</span>
            </div>
            <h3 class="mt-4 font-extrabold text-lg">Reportes</h3>
            <p class="mt-1 text-slate-600">PDF + QR (próximamente).</p>
            <div class="mt-4 text-sm font-semibold text-amber-700">Listo para integrar</div>
        </div>
    </div>

    {{-- Banner --}}
    <div class="mt-6 rounded-3xl bg-gradient-to-r from-pink-600 via-amber-500 to-sky-600 p-6 text-white shadow-sm">
        <h4 class="text-lg font-extrabold">Tip del día 🍓</h4>
        <p class="mt-1 text-white/90">
            Empieza por cargar tu catálogo de productos, luego registra ventas y al final generamos el PDF con QR.
        </p>
    </div>
@endsection
