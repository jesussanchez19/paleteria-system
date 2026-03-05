@extends('layouts.app')

@section('title', 'Dashboard Inteligente')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="max-w-6xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold">Dashboard Inteligente 📊</h1>
            <p class="text-slate-600">Análisis de ventas y predicciones</p>
        </div>
        <a href="{{ route('panel.index') }}" class="px-4 py-2 rounded-xl bg-white border border-slate-200 font-bold hover:bg-slate-50 transition">
            ← Volver al panel
        </a>
    </div>

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
        
        <div class="bg-gradient-to-br from-amber-500 to-orange-500 rounded-2xl p-4 shadow-sm text-white">
            <p class="text-sm opacity-90">🔮 Predicción mañana</p>
            <p class="text-2xl font-extrabold">${{ number_format($prediction, 0) }}</p>
            <p class="text-xs opacity-75 mt-1">Basado en tendencia</p>
        </div>
    </div>

    {{-- Alertas rápidas --}}
    @if($stats['productos_bajo_stock'] > 0)
    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 flex items-center gap-3">
        <span class="text-2xl">⚠️</span>
        <div>
            <p class="font-bold text-amber-800">{{ $stats['productos_bajo_stock'] }} productos con stock bajo</p>
            <p class="text-sm text-amber-700">Revisa el inventario para evitar faltantes</p>
        </div>
    </div>
    @endif

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
