@extends('layouts.app')

@section('title','Panel')

@section('content')
<style>
    .panel-card {
        background: white;
        border: 1px solid rgba(99, 102, 241, 0.1);
        border-radius: 1rem;
        padding: 1.5rem;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    .panel-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #6366f1, #8b5cf6);
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    .panel-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 40px rgba(99, 102, 241, 0.15);
        border-color: rgba(99, 102, 241, 0.3);
    }
    .panel-card:hover::before {
        opacity: 1;
    }
    .panel-card-featured {
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #a855f7 100%);
        border: none;
        color: white;
    }
    .panel-card-featured::before {
        display: none;
    }
    .panel-card-featured:hover {
        box-shadow: 0 12px 40px rgba(99, 102, 241, 0.4);
    }
    .panel-card-climate {
        background: linear-gradient(135deg, #0ea5e9 0%, #3b82f6 50%, #6366f1 100%);
        border: none;
        color: white;
    }
    .panel-card-climate::before {
        display: none;
    }
    .panel-card-climate:hover {
        box-shadow: 0 12px 40px rgba(14, 165, 233, 0.4);
    }
    .icon-box {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        background: linear-gradient(135deg, rgba(99, 102, 241, 0.1), rgba(139, 92, 246, 0.1));
    }
    .icon-box-white {
        background: rgba(255, 255, 255, 0.2);
    }
</style>

<div class="space-y-8">

    {{-- Header --}}
    <div class="bg-gradient-to-r from-indigo-600 via-violet-600 to-purple-600 rounded-2xl p-6 sm:p-8 text-white shadow-xl">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold">Panel de Gestión</h1>
                <p class="text-indigo-100 mt-1">
                    Bienvenido, <span class="font-semibold text-white">{{ auth()->user()->name }}</span>
                </p>
            </div>
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center px-4 py-2 rounded-full bg-white/20 backdrop-blur-sm text-sm font-semibold">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    {{ ucfirst(auth()->user()->role) }}
                </span>
            </div>
        </div>
    </div>

    @if(auth()->user()->isGerente())
    {{-- Stats rápidos --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl p-4 border border-slate-100 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-green-400 to-emerald-500 flex items-center justify-center text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-slate-500 font-medium">Ventas hoy</p>
                    <p class="text-lg font-bold text-slate-800">${{ number_format(\App\Models\Sale::whereDate('created_at', today())->sum('total'), 0) }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-4 border border-slate-100 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-blue-400 to-indigo-500 flex items-center justify-center text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-slate-500 font-medium">Transacciones</p>
                    <p class="text-lg font-bold text-slate-800">{{ \App\Models\Sale::whereDate('created_at', today())->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-4 border border-slate-100 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-slate-500 font-medium">Productos</p>
                    <p class="text-lg font-bold text-slate-800">{{ \App\Models\Product::where('is_active', true)->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-4 border border-slate-100 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-violet-400 to-purple-500 flex items-center justify-center text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-slate-500 font-medium">Vendedores</p>
                    <p class="text-lg font-bold text-slate-800">{{ \App\Models\User::where('role', 'vendedor')->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Accesos principales --}}
    <div>
        <h2 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
            </svg>
            Accesos rápidos
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">

            {{-- Dashboard --}}
            <a href="{{ route('panel.dashboard') }}" class="panel-card panel-card-featured group">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="icon-box icon-box-white mb-4">📊</div>
                        <h3 class="text-lg font-bold">Dashboard</h3>
                        <p class="text-sm opacity-80 mt-1">Gráficas y predicciones de ventas con IA.</p>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-white/20 flex items-center justify-between">
                    <span class="text-sm font-semibold">Ver análisis</span>
                    <svg class="w-5 h-5 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </div>
            </a>
            
            {{-- POS --}}
            <a href="{{ route('pos.index') }}" class="panel-card group">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="icon-box mb-4">🧾</div>
                        <h3 class="text-lg font-bold text-slate-800 group-hover:text-indigo-600 transition-colors">POS / Ventas</h3>
                        <p class="text-sm text-slate-500 mt-1">Registrar ventas y generar totales.</p>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-slate-100 flex items-center justify-between text-slate-600 group-hover:text-indigo-600 transition-colors">
                    <span class="text-sm font-semibold">Ir a POS</span>
                    <svg class="w-5 h-5 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </div>
            </a>

            {{-- Productos --}}
            <a href="{{ route('products.index') }}" class="panel-card group">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="icon-box mb-4">🍦</div>
                        <h3 class="text-lg font-bold text-slate-800 group-hover:text-indigo-600 transition-colors">Productos</h3>
                        <p class="text-sm text-slate-500 mt-1">Catálogo interno y control de inventario.</p>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-slate-100 flex items-center justify-between text-slate-600 group-hover:text-indigo-600 transition-colors">
                    <span class="text-sm font-semibold">Ver productos</span>
                    <svg class="w-5 h-5 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </div>
            </a>

            {{-- Vendedores --}}
            <a href="{{ route('vendedores.index') }}" class="panel-card group">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="icon-box mb-4">👥</div>
                        <h3 class="text-lg font-bold text-slate-800 group-hover:text-indigo-600 transition-colors">Vendedores</h3>
                        <p class="text-sm text-slate-500 mt-1">Crear cuentas para el personal.</p>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-slate-100 flex items-center justify-between text-slate-600 group-hover:text-indigo-600 transition-colors">
                    <span class="text-sm font-semibold">Gestionar vendedores</span>
                    <svg class="w-5 h-5 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </div>
            </a>

            {{-- Reportes --}}
            <a href="{{ route('reportes.index') }}" class="panel-card group">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="icon-box mb-4">📈</div>
                        <h3 class="text-lg font-bold text-slate-800 group-hover:text-indigo-600 transition-colors">Reportes</h3>
                        <p class="text-sm text-slate-500 mt-1">Ventas del día y métricas detalladas.</p>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-slate-100 flex items-center justify-between text-slate-600 group-hover:text-indigo-600 transition-colors">
                    <span class="text-sm font-semibold">Ver reportes</span>
                    <svg class="w-5 h-5 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </div>
            </a>

            {{-- Caja --}}
            <a href="{{ route('panel.caja.index') }}" class="panel-card group">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="icon-box mb-4">💵</div>
                        <h3 class="text-lg font-bold text-slate-800 group-hover:text-indigo-600 transition-colors">Caja</h3>
                        <p class="text-sm text-slate-500 mt-1">Cortes, turnos y diferencias.</p>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-slate-100 flex items-center justify-between text-slate-600 group-hover:text-indigo-600 transition-colors">
                    <span class="text-sm font-semibold">Ver caja</span>
                    <svg class="w-5 h-5 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </div>
            </a>

        </div>
    </div>

    {{-- Herramientas adicionales --}}
    <div>
        <h2 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            Herramientas
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">

            {{-- Análisis de Clima --}}
            <a href="{{ route('panel.weather.insight') }}" class="panel-card panel-card-climate group">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="icon-box icon-box-white mb-4">🌤️</div>
                        <h3 class="text-lg font-bold">Análisis de Clima</h3>
                        <p class="text-sm opacity-80 mt-1">Recomendaciones basadas en temperatura.</p>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-white/20 flex items-center justify-between">
                    <span class="text-sm font-semibold">Ver análisis</span>
                    <svg class="w-5 h-5 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </div>
            </a>

            {{-- Bitácora --}}
            <a href="{{ route('panel.bitacora') }}" class="panel-card group">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="icon-box mb-4">📋</div>
                        <h3 class="text-lg font-bold text-slate-800 group-hover:text-indigo-600 transition-colors">Bitácora</h3>
                        <p class="text-sm text-slate-500 mt-1">Registro de acciones del sistema.</p>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-slate-100 flex items-center justify-between text-slate-600 group-hover:text-indigo-600 transition-colors">
                    <span class="text-sm font-semibold">Ver bitácora</span>
                    <svg class="w-5 h-5 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </div>
            </a>

            {{-- Configuración --}}
            <a href="{{ route('panel.config') }}" class="panel-card group">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="icon-box mb-4">⚙️</div>
                        <h3 class="text-lg font-bold text-slate-800 group-hover:text-indigo-600 transition-colors">Configuración</h3>
                        <p class="text-sm text-slate-500 mt-1">Parámetros del negocio y ubicación.</p>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-slate-100 flex items-center justify-between text-slate-600 group-hover:text-indigo-600 transition-colors">
                    <span class="text-sm font-semibold">Ajustes</span>
                    <svg class="w-5 h-5 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </div>
            </a>

        </div>
    </div>
    @endif

</div>
@endsection
