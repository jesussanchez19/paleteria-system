@extends('layouts.app')
@section('title', 'Gráficas')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold">Reportes y gráficas 📊</h1>
            <p class="text-slate-600">Fecha base (hoy): <b>{{ $today }}</b></p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('reporte.diario') }}"
               class="px-4 py-2 rounded-xl bg-white border border-slate-200 font-bold text-slate-800 hover:bg-slate-50 transition">
                Ver reporte diario
            </a>

            <a href="{{ route('panel.index') }}"
               class="px-4 py-2 rounded-xl bg-white border border-slate-200 font-bold text-slate-800 hover:bg-slate-50 transition">
                ← Volver
            </a>

            {{-- Botón cerrar sesión removido de esta vista --}}
        </div>
    </div>

    {{-- Resumen rápido --}}
    @if(Route::currentRouteName() !== 'reportes.graficas')
    <div class="grid grid-cols-2 md:grid-cols-2 gap-4">
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <p class="text-sm text-slate-600">Hoy (ventas)</p>
            <p class="text-2xl font-extrabold">{{ $compare['today']['qty'] }}</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <p class="text-sm text-slate-600">Ayer (ventas)</p>
            <p class="text-2xl font-extrabold">{{ $compare['yesterday']['qty'] }}</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <p class="text-sm text-slate-600">Hoy (total)</p>
            <p class="text-2xl font-extrabold">${{ number_format($compare['today']['total'], 2) }}</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <p class="text-sm text-slate-600">Ayer (total)</p>
            <p class="text-2xl font-extrabold">${{ number_format($compare['yesterday']['total'], 2) }}</p>
        </div>
    </div>
    @endif

    {{-- Solo mostrar las dos gráficas principales aquí --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <h2 class="text-lg font-extrabold">Ventas por hora (hoy)</h2>
            <p class="text-sm text-slate-600">Cantidad de ventas por hora.</p>
            <div class="mt-4">
                <canvas id="chartSalesByHour" height="140"></canvas>
            </div>
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <h2 class="text-lg font-extrabold">Top productos (hoy)</h2>
            <p class="text-sm text-slate-600">Top 10 por cantidad.</p>
            <div class="mt-4">
                <canvas id="chartTopProducts" height="140"></canvas>
            </div>
        </div>
    </div>

    @if(Route::currentRouteName() !== 'reportes.graficas')
    <div class="mt-8 flex justify-center">
        <a href="{{ route('reportes.graficas') }}" class="px-6 py-3 rounded-xl bg-pink-500 text-white font-bold hover:bg-pink-600 transition text-lg">Ver todas las gráficas y detalles</a>
    </div>
    @endif

    @if(Route::currentRouteName() === 'reportes.graficas')
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <h2 class="text-lg font-extrabold">Ventas por vendedor (hoy)</h2>
            <p class="text-sm text-slate-600">Total vendido por usuario.</p>
            <div class="mt-4">
                <canvas id="chartSalesBySeller" height="140"></canvas>
            </div>
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <h2 class="text-lg font-extrabold">Ingresos por categoría (hoy)</h2>
            <p class="text-sm text-slate-600">Total vendido por categoría.</p>
            <div class="mt-4">
                <canvas id="chartRevenueByCategory" height="140"></canvas>
            </div>
        </div>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <h2 class="text-lg font-extrabold">Ventas totales (últimos 7 días)</h2>
            <p class="text-sm text-slate-600">Total vendido por día.</p>
            <div class="mt-4">
                <canvas id="chartSalesLast7Days" height="140"></canvas>
            </div>
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <h2 class="text-lg font-extrabold">Ticket promedio (últimos 7 días)</h2>
            <p class="text-sm text-slate-600">Promedio de venta por ticket.</p>
            <div class="mt-4">
                <canvas id="chartAvgTicketLast7Days" height="140"></canvas>
            </div>
        </div>
    </div>
    {{-- Productos sin stock --}}
    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
        <h2 class="text-lg font-extrabold">Productos sin stock</h2>
        <p class="text-sm text-slate-600">Stock actual ≤ 0 (activos).</p>
        @if($outOfStock->isEmpty())
            <p class="text-slate-600 mt-3">No hay productos sin stock.</p>
        @else
            <div class="mt-3 overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="text-left text-slate-600 border-b">
                            <th class="py-2 pr-4">Producto</th>
                            <th class="py-2 pr-4">Categoría</th>
                            <th class="py-2 pr-4 text-right">Stock</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($outOfStock as $p)
                            <tr class="border-b last:border-b-0">
                                <td class="py-3 pr-4 font-bold">{{ $p->name }}</td>
                                <td class="py-3 pr-4">{{ $p->category ?? 'Sin categoría' }}</td>
                                <td class="py-3 pr-4 text-right font-extrabold text-rose-700">{{ (int)$p->stock }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
    @endif

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // ===== Datos del backend =====
    const salesByHour = @json($salesByHour);
    const topProducts = @json($topProducts);
    const salesLast7Days = @json($salesLast7Days);
    const revenueByCategory = @json($revenueByCategory);
    const salesBySeller = @json($salesBySeller);
    const avgTicketLast7Days = @json($avgTicketLast7Days);

    // ===== 1) Ventas por hora =====
    const hourLabels = salesByHour.map(x => x.hour + ':00');
    const hourQty = salesByHour.map(x => Number(x.qty));

    new Chart(document.getElementById('chartSalesByHour'), {
        type: 'line',
        data: {
            labels: hourLabels,
            datasets: [{ label: 'Ventas (cantidad)', data: hourQty, tension: 0.3 }]
        },
        options: { responsive: true, scales: { y: { beginAtZero: true } } }
    });

    // ===== 2) Top productos =====
    const prodLabels = topProducts.map(x => x.name);
    const prodQty = topProducts.map(x => Number(x.qty));

    new Chart(document.getElementById('chartTopProducts'), {
        type: 'bar',
        data: {
            labels: prodLabels,
            datasets: [{ label: 'Cantidad vendida', data: prodQty }]
        },
        options: { responsive: true, scales: { y: { beginAtZero: true } } }
    });

    // ===== 3) Ventas últimos 7 días =====
    const dayLabels = salesLast7Days.map(x => x.day);
    const dayTotals = salesLast7Days.map(x => Number(x.total));

    new Chart(document.getElementById('chartSalesLast7Days'), {
        type: 'line',
        data: {
            labels: dayLabels,
            datasets: [{ label: 'Total vendido', data: dayTotals, tension: 0.3 }]
        },
        options: { responsive: true, scales: { y: { beginAtZero: true } } }
    });

    // ===== 4) Ingresos por categoría =====
    const catLabels = revenueByCategory.map(x => x.category);
    const catTotals = revenueByCategory.map(x => Number(x.total));

    new Chart(document.getElementById('chartRevenueByCategory'), {
        type: 'doughnut',
        data: {
            labels: catLabels,
            datasets: [{ label: 'Total', data: catTotals }]
        },
        options: { responsive: true }
    });

    // ===== 5) Ventas por vendedor =====
    const sellerLabels = salesBySeller.map(x => x.name);
    const sellerTotals = salesBySeller.map(x => Number(x.total));

    new Chart(document.getElementById('chartSalesBySeller'), {
        type: 'bar',
        data: {
            labels: sellerLabels,
            datasets: [{ label: 'Total vendido', data: sellerTotals }]
        },
        options: { responsive: true, scales: { y: { beginAtZero: true } } }
    });

    // ===== 6) Ticket promedio últimos 7 días =====
    const avgLabels = avgTicketLast7Days.map(x => x.day);
    const avgTotals = avgTicketLast7Days.map(x => Number(x.avg_total));

    new Chart(document.getElementById('chartAvgTicketLast7Days'), {
        type: 'line',
        data: {
            labels: avgLabels,
            datasets: [{ label: 'Ticket promedio', data: avgTotals, tension: 0.3 }]
        },
        options: { responsive: true, scales: { y: { beginAtZero: true } } }
    });
</script>
@endsection
