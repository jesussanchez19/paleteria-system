@extends('layouts.app')

@section('title', 'Dashboard Inteligente')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="max-w-6xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold">Dashboard Inteligente 📊</h1>
            <p class="text-slate-600">Análisis de ventas con IA</p>
        </div>
        <a href="{{ route('panel.index') }}" class="px-4 py-2 rounded-xl bg-white border border-slate-200 font-bold hover:bg-slate-50 transition">
            ← Volver al panel
        </a>
    </div>

    {{-- ============================= --}}
    {{-- 1. RESUMEN IA DEL DÍA --}}
    {{-- ============================= --}}
    <div class="bg-gradient-to-br from-violet-600 to-indigo-700 rounded-2xl p-5 shadow-lg text-white">
        <div class="flex items-start gap-4">
            <span class="text-4xl">🤖</span>
            <div class="flex-1">
                <h2 class="font-extrabold text-lg mb-2">Resumen IA del día</h2>
                <p class="text-white/90 leading-relaxed">{{ $resumenIA }}</p>
                @if($productoEstrella)
                <p class="mt-3 text-sm text-white/70">
                    ⭐ Producto estrella hoy: <span class="font-bold text-white">{{ $productoEstrella->name }}</span> 
                    ({{ $productoEstrella->qty }} vendidos)
                </p>
                @endif
            </div>
        </div>
    </div>

    {{-- ============================= --}}
    {{-- 2. ALERTAS INTELIGENTES --}}
    {{-- ============================= --}}
    @if(count($alertas) > 0)
    <div class="space-y-3">
        @foreach($alertas as $alerta)
        <div class="rounded-xl p-4 flex items-center gap-3 border
            {{ $alerta['tipo'] === 'danger' ? 'bg-rose-50 border-rose-200' : '' }}
            {{ $alerta['tipo'] === 'warning' ? 'bg-amber-50 border-amber-200' : '' }}
            {{ $alerta['tipo'] === 'info' ? 'bg-blue-50 border-blue-200' : '' }}
        ">
            <span class="text-2xl">{{ $alerta['icono'] }}</span>
            <div>
                <p class="font-bold 
                    {{ $alerta['tipo'] === 'danger' ? 'text-rose-800' : '' }}
                    {{ $alerta['tipo'] === 'warning' ? 'text-amber-800' : '' }}
                    {{ $alerta['tipo'] === 'info' ? 'text-blue-800' : '' }}
                ">{{ $alerta['titulo'] }}</p>
                <p class="text-sm
                    {{ $alerta['tipo'] === 'danger' ? 'text-rose-700' : '' }}
                    {{ $alerta['tipo'] === 'warning' ? 'text-amber-700' : '' }}
                    {{ $alerta['tipo'] === 'info' ? 'text-blue-700' : '' }}
                ">{{ $alerta['mensaje'] }}</p>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- KPIs principales --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
            <p class="text-sm text-slate-500">Ventas hoy</p>
            <p class="text-2xl font-extrabold text-emerald-600">${{ number_format($stats['ventas_hoy'], 0) }}</p>
            <p class="text-xs mt-1 {{ $stats['cambio_vs_ayer'] >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                {{ $stats['cambio_vs_ayer'] >= 0 ? '↑' : '↓' }} {{ abs($stats['cambio_vs_ayer']) }}% vs ayer
            </p>
        </div>
        
        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
            <p class="text-sm text-slate-500">Transacciones</p>
            <p class="text-2xl font-extrabold text-blue-600">{{ $stats['transacciones_hoy'] }}</p>
            <p class="text-xs text-slate-400 mt-1">Hoy</p>
        </div>
        
        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
            <p class="text-sm text-slate-500">Ticket promedio</p>
            <p class="text-2xl font-extrabold text-violet-600">${{ number_format($stats['ticket_promedio'], 0) }}</p>
            <p class="text-xs text-slate-400 mt-1">Hoy</p>
        </div>
        
        {{-- 5. PREDICCIÓN CON CONTEXTO --}}
        <div class="bg-gradient-to-br from-amber-500 to-orange-500 rounded-2xl p-4 shadow-sm text-white">
            <p class="text-sm opacity-90">🔮 Predicción mañana</p>
            <p class="text-2xl font-extrabold">${{ number_format($prediction, 0) }}</p>
            <p class="text-xs opacity-75 mt-1">{{ $prediccionContexto }}</p>
        </div>
    </div>

    {{-- ============================= --}}
    {{-- 3. RECOMENDACIONES --}}
    {{-- ============================= --}}
    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
        <h2 class="font-extrabold text-slate-800 mb-4">💡 Recomendaciones</h2>
        @if(count($recomendaciones) > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            @foreach($recomendaciones as $rec)
            <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl">
                <span class="text-2xl">{{ $rec['icono'] }}</span>
                <p class="text-sm text-slate-700">{{ $rec['texto'] }}</p>
            </div>
            @endforeach
        </div>
        @else
        <p class="text-slate-500">No hay recomendaciones activas</p>
        @endif
        
        @if($weather)
        <div class="mt-4 pt-4 border-t border-slate-200">
            <p class="text-xs text-slate-500">
                🌡️ Clima actual: <span class="font-bold">{{ $weather->temp }}°C</span>, {{ $weather->condition }}
            </p>
        </div>
        @endif
    </div>

    {{-- Gráficas principales --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        {{-- Ventas últimos 7 días --}}
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <h2 class="font-extrabold text-slate-800 mb-4">📈 Ventas últimos 7 días</h2>
            <div class="h-64">
                <canvas id="salesChart"></canvas>
            </div>
        </div>

        {{-- Top productos --}}
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <h2 class="font-extrabold text-slate-800 mb-4">🏆 Top 5 productos (30 días)</h2>
            <div class="h-64">
                <canvas id="productsChart"></canvas>
            </div>
        </div>

        {{-- Ventas por hora (hoy) --}}
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <h2 class="font-extrabold text-slate-800 mb-4">⏰ Ventas por hora (hoy)</h2>
            <div class="h-64">
                <canvas id="hourlyChart"></canvas>
            </div>
        </div>

        {{-- Ventas por categoría --}}
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <h2 class="font-extrabold text-slate-800 mb-4">🏷️ Ventas por categoría (30 días)</h2>
            <div class="h-64">
                <canvas id="categoryChart"></canvas>
            </div>
        </div>

    </div>

    {{-- Tabla resumen últimos 7 días --}}
    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
        <h2 class="font-extrabold text-slate-800 mb-4">📅 Detalle últimos 7 días</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-200">
                        <th class="text-left py-2 font-bold text-slate-600">Día</th>
                        <th class="text-right py-2 font-bold text-slate-600">Fecha</th>
                        <th class="text-right py-2 font-bold text-slate-600">Transacciones</th>
                        <th class="text-right py-2 font-bold text-slate-600">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($completeSales as $day)
                    <tr class="border-b border-slate-100 hover:bg-slate-50">
                        <td class="py-2 capitalize">{{ $day['day_name'] }}</td>
                        <td class="py-2 text-right text-slate-600">{{ $day['date'] }}</td>
                        <td class="py-2 text-right">{{ $day['transacciones'] }}</td>
                        <td class="py-2 text-right font-bold">${{ number_format($day['total'], 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="border-t-2 border-slate-300">
                        <td class="py-2 font-extrabold" colspan="2">Total semana</td>
                        <td class="py-2 text-right font-bold">{{ $completeSales->sum('transacciones') }}</td>
                        <td class="py-2 text-right font-extrabold text-emerald-600">${{ number_format($completeSales->sum('total'), 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

</div>

<script>
// Configuración global de Chart.js
Chart.defaults.font.family = 'system-ui, -apple-system, sans-serif';
Chart.defaults.plugins.legend.display = false;

// Datos
const salesData = @json($completeSales);
const topProducts = @json($topProducts);
const hourlyData = @json($hourlyData);
const categoryData = @json($salesByCategory);

// Colores
const colors = {
    primary: 'rgb(236, 72, 153)', // pink-500
    primaryLight: 'rgba(236, 72, 153, 0.1)',
    secondary: 'rgb(59, 130, 246)', // blue-500
    success: 'rgb(16, 185, 129)', // emerald-500
    violet: 'rgb(139, 92, 246)', // violet-500
};

const chartColors = [
    'rgb(236, 72, 153)',
    'rgb(59, 130, 246)',
    'rgb(16, 185, 129)',
    'rgb(245, 158, 11)',
    'rgb(139, 92, 246)',
];

// Gráfica de ventas últimos 7 días
new Chart(document.getElementById('salesChart'), {
    type: 'line',
    data: {
        labels: salesData.map(x => x.day_name),
        datasets: [{
            label: 'Ventas',
            data: salesData.map(x => x.total),
            borderColor: colors.primary,
            backgroundColor: colors.primaryLight,
            fill: true,
            tension: 0.4,
            pointBackgroundColor: colors.primary,
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointRadius: 5,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: value => '$' + value.toLocaleString()
                }
            }
        },
        plugins: {
            tooltip: {
                callbacks: {
                    label: ctx => '$' + ctx.raw.toLocaleString()
                }
            }
        }
    }
});

// Gráfica top productos
new Chart(document.getElementById('productsChart'), {
    type: 'bar',
    data: {
        labels: topProducts.map(x => x.name.length > 15 ? x.name.substring(0, 15) + '...' : x.name),
        datasets: [{
            label: 'Unidades vendidas',
            data: topProducts.map(x => x.qty),
            backgroundColor: chartColors,
            borderRadius: 8,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        indexAxis: 'y',
        scales: {
            x: {
                beginAtZero: true
            }
        }
    }
});

// Gráfica por hora
new Chart(document.getElementById('hourlyChart'), {
    type: 'bar',
    data: {
        labels: hourlyData.map(x => x.hora),
        datasets: [{
            label: 'Ventas',
            data: hourlyData.map(x => x.total),
            backgroundColor: colors.secondary,
            borderRadius: 4,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: value => '$' + value.toLocaleString()
                }
            }
        }
    }
});

// Gráfica por categoría (dona)
new Chart(document.getElementById('categoryChart'), {
    type: 'doughnut',
    data: {
        labels: categoryData.map(x => x.category || 'Sin categoría'),
        datasets: [{
            data: categoryData.map(x => x.revenue),
            backgroundColor: chartColors,
            borderWidth: 2,
            borderColor: '#fff',
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: true,
                position: 'right'
            },
            tooltip: {
                callbacks: {
                    label: ctx => ctx.label + ': $' + ctx.raw.toLocaleString()
                }
            }
        }
    }
});
</script>

@endsection
