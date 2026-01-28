@extends('layouts.app')

@section('title','Panel')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold">Panel de Gestión 🍧</h1>
            <p class="text-slate-600">
                Bienvenido, <b>{{ auth()->user()->name }}</b>
                <span class="ml-2 inline-flex items-center px-2 py-1 rounded-full bg-slate-100 border border-slate-200 text-xs font-bold text-slate-700">
                    Rol: {{ auth()->user()->role }}
                </span>
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('catalogo.index') }}"
               class="px-4 py-2 rounded-xl bg-white border border-slate-200 font-bold text-slate-800 hover:bg-slate-50 transition">
                Ver catálogo
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="px-4 py-2 rounded-xl bg-slate-900 text-white font-extrabold hover:bg-slate-800 transition">
                    Cerrar sesión
                </button>
            </form>
        </div>
    </div>

    {{-- Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

        {{-- POS --}}
        <a href="{{ route('pos.index') }}"
           class="group bg-white border border-slate-200 rounded-2xl p-5 shadow-sm hover:shadow-md transition">
            <div class="flex items-start justify-between">
                <div>
                    <h2 class="text-lg font-extrabold group-hover:text-pink-700">POS / Ventas</h2>
                    <p class="text-sm text-slate-600">Registrar ventas y generar totales.</p>
                </div>
                <span class="text-2xl">🧾</span>
            </div>
            <div class="mt-4 text-sm font-bold text-slate-700 group-hover:text-pink-700">
                Ir a POS →
            </div>
        </a>

        {{-- Productos --}}
        <a href="{{ route('products.index') }}"
           class="group bg-white border border-slate-200 rounded-2xl p-5 shadow-sm hover:shadow-md transition">
            <div class="flex items-start justify-between">
                <div>
                    <h2 class="text-lg font-extrabold group-hover:text-emerald-700">Productos</h2>
                    <p class="text-sm text-slate-600">Catálogo interno y control.</p>
                </div>
                <span class="text-2xl">🍦</span>
            </div>
            <div class="mt-4 text-sm font-bold text-slate-700 group-hover:text-emerald-700">
                Ver productos →
            </div>
        </a>

        {{-- Vendedores (solo gerente/admin, pero ya estás dentro del panel) --}}
        <a href="{{ route('vendedores.index') }}"
           class="group bg-white border border-slate-200 rounded-2xl p-5 shadow-sm hover:shadow-md transition">
            <div class="flex items-start justify-between">
                <div>
                    <h2 class="text-lg font-extrabold group-hover:text-indigo-700">Vendedores</h2>
                    <p class="text-sm text-slate-600">Crear cuentas para el personal.</p>
                </div>
                <span class="text-2xl">👥</span>
            </div>
            <div class="mt-4 text-sm font-bold text-slate-700 group-hover:text-indigo-700">
                Gestionar vendedores →
            </div>
        </a>

        {{-- Reportes --}}
        <a href="{{ route('reportes.index') }}"
           class="group bg-white border border-slate-200 rounded-2xl p-5 shadow-sm hover:shadow-md transition">
            <div class="flex items-start justify-between">
                <div>
                    <h2 class="text-lg font-extrabold group-hover:text-sky-700">Reportes</h2>
                    <p class="text-sm text-slate-600">Ventas del día y métricas.</p>
                </div>
                <span class="text-2xl">📊</span>
            </div>
            <div class="mt-4 text-sm font-bold text-slate-700 group-hover:text-sky-700">
                Ver reportes →
            </div>
        </a>

        {{-- IA --}}
        <a href="{{ route('ia.index') }}"
           class="group bg-white border border-slate-200 rounded-2xl p-5 shadow-sm hover:shadow-md transition">
            <div class="flex items-start justify-between">
                <div>
                    <h2 class="text-lg font-extrabold group-hover:text-violet-700">IA / Asistente</h2>
                    <p class="text-sm text-slate-600">Preguntas sobre ventas y clima.</p>
                </div>
                <span class="text-2xl">🤖</span>
            </div>
            <div class="mt-4 text-sm font-bold text-slate-700 group-hover:text-violet-700">
                Abrir IA →
            </div>
        </a>

        {{-- Configuración (admin y gerente) --}}
        @if(auth()->user()->role === 'admin' || auth()->user()->role === 'gerente')
        <a href="{{ route('config.index') }}"
           class="group bg-white border border-slate-200 rounded-2xl p-5 shadow-sm hover:shadow-md transition">
            <div class="flex items-start justify-between">
                <div>
                    <h2 class="text-lg font-extrabold group-hover:text-amber-700">Configuración</h2>
                    <p class="text-sm text-slate-600">Parámetros del negocio.</p>
                </div>
                <span class="text-2xl">⚙️</span>
            </div>
            <div class="mt-4 text-sm font-bold text-slate-700 group-hover:text-amber-700">
                Ajustes →
            </div>
        </a>
        @endif

        {{-- Config Crítica (solo admin) --}}
        @if(auth()->user()->role === 'admin')
        <a href="{{ route('config.critical') }}"
           class="group bg-white border border-amber-200 rounded-2xl p-5 shadow-sm hover:shadow-md transition">
            <div class="flex items-start justify-between">
                <div>
                    <h2 class="text-lg font-extrabold group-hover:text-amber-700">Config Crítica</h2>
                    <p class="text-sm text-slate-600">Acciones delicadas del sistema.</p>
                </div>
                <span class="text-2xl">🔒</span>
            </div>
            <div class="mt-4 text-sm font-bold text-slate-700 group-hover:text-amber-700">
                Crítico →
            </div>
        </a>
        @endif

    </div>

</div>
@endsection
