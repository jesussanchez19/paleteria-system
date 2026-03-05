@extends('layouts.app')
@section('title', 'Gráficas')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold">Reportes y gráficas 📊</h1>
            <p class="text-slate-600">
                Período: <b>{{ $periodoLabel }}</b>
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('reportes.pdf', ['start_date' => $startDate ?? $today, 'end_date' => $endDate ?? $today]) }}"
               class="px-4 py-2 rounded-xl bg-rose-500 text-white font-bold hover:bg-rose-600 transition">
                📄 Descargar PDF
            </a>

            @if(!empty($qrBase64))
            <div class="flex items-center gap-2 p-1 bg-white border border-slate-200 rounded-lg" title="Escanea para descargar PDF">
              <img src="{{ $qrBase64 }}" alt="QR" style="width: 64px; height: 64px;">
            </div>
            @endif

            <a href="{{ route('reporte.diario') }}"
               class="px-4 py-2 rounded-xl bg-white border border-slate-200 font-bold text-slate-800 hover:bg-slate-50 transition">
                Ver reporte diario
            </a>

            <a href="{{ route('reportes.index', ['start_date' => $startDate ?? $today, 'end_date' => $endDate ?? $today]) }}"
               class="px-4 py-2 rounded-xl bg-white border border-slate-200 font-bold text-slate-800 hover:bg-slate-50 transition">
                ← Volver a reportes
            </a>
        </div>
    </div>

    {{-- Selector de rango de fechas - Solo en vista principal de reportes --}}
    @if(Route::currentRouteName() !== 'reportes.graficas')
    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
        <form method="GET" action="{{ route('reportes.index') }}" class="flex flex-wrap items-end gap-4">
            <div>
                <label for="start_date" class="block text-sm font-medium text-slate-700 mb-1">Fecha inicio</label>
                <input type="date" name="start_date" id="start_date" 
                       value="{{ $startDate ?? $today }}"
                       class="px-3 py-2 border border-slate-300 rounded-lg focus:ring-pink-500 focus:border-pink-500">
            </div>
            <div>
                <label for="end_date" class="block text-sm font-medium text-slate-700 mb-1">Fecha fin</label>
                <input type="date" name="end_date" id="end_date" 
                       value="{{ $endDate ?? $today }}"
                       class="px-3 py-2 border border-slate-300 rounded-lg focus:ring-pink-500 focus:border-pink-500">
            </div>
            <button type="submit" 
                    class="px-4 py-2 bg-pink-500 text-white font-bold rounded-lg hover:bg-pink-600 transition">
                Filtrar
            </button>
            <a href="{{ route('reportes.index') }}" 
               class="px-4 py-2 bg-slate-200 text-slate-700 font-bold rounded-lg hover:bg-slate-300 transition">
                Hoy
            </a>
            <a href="{{ route('reportes.index', ['start_date' => now()->startOfWeek()->toDateString(), 'end_date' => now()->toDateString()]) }}" 
               class="px-4 py-2 bg-slate-200 text-slate-700 font-bold rounded-lg hover:bg-slate-300 transition">
                Esta semana
            </a>
            <a href="{{ route('reportes.index', ['start_date' => now()->startOfMonth()->toDateString(), 'end_date' => now()->toDateString()]) }}" 
               class="px-4 py-2 bg-slate-200 text-slate-700 font-bold rounded-lg hover:bg-slate-300 transition">
                Este mes
            </a>
        </form>
    </div>
    @endif

    {{-- Resumen rápido --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <p class="text-sm text-slate-600">{{ ($isRange ?? false) ? 'Período' : 'Hoy' }} (ventas)</p>
            <p class="text-2xl font-extrabold">{{ $compare['today']['qty'] }}</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <p class="text-sm text-slate-600">{{ ($isRange ?? false) ? 'Período anterior' : 'Ayer' }} (ventas)</p>
            <p class="text-2xl font-extrabold">{{ $compare['yesterday']['qty'] }}</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <p class="text-sm text-slate-600">{{ ($isRange ?? false) ? 'Período' : 'Hoy' }} (total)</p>
            <p class="text-2xl font-extrabold">${{ number_format($compare['today']['total'], 2) }}</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <p class="text-sm text-slate-600">{{ ($isRange ?? false) ? 'Período anterior' : 'Ayer' }} (total)</p>
            <p class="text-2xl font-extrabold">${{ number_format($compare['yesterday']['total'], 2) }}</p>
        </div>
    </div>

    {{-- Solo mostrar las dos gráficas principales aquí --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <h2 class="text-lg font-extrabold">Ventas por hora ({{ $periodoLabel }})</h2>
            <p class="text-sm text-slate-600">Cantidad de ventas por hora.</p>
            <div class="mt-4">
                <canvas id="chartSalesByHour" height="140"></canvas>
            </div>
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <h2 class="text-lg font-extrabold">Top productos ({{ $periodoLabel }})</h2>
            <p class="text-sm text-slate-600">Top 10 por cantidad.</p>
            <div class="mt-4">
                <canvas id="chartTopProducts" height="140"></canvas>
            </div>
        </div>
    </div>

    @if(Route::currentRouteName() !== 'reportes.graficas')
    <div class="mt-8 flex justify-center">
        <a href="{{ route('reportes.graficas', ['start_date' => $startDate ?? $today, 'end_date' => $endDate ?? $today]) }}" class="px-6 py-3 rounded-xl bg-pink-500 text-white font-bold hover:bg-pink-600 transition text-lg">Ver todas las gráficas y detalles</a>
    </div>
    @endif

    @if(Route::currentRouteName() === 'reportes.graficas')
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <h2 class="text-lg font-extrabold">Ventas por vendedor ({{ $periodoLabel }})</h2>
            <p class="text-sm text-slate-600">Total vendido por usuario.</p>
            <div class="mt-4">
                <canvas id="chartSalesBySeller" height="140"></canvas>
            </div>
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <h2 class="text-lg font-extrabold">Ingresos por categoría ({{ $periodoLabel }})</h2>
            <p class="text-sm text-slate-600">Total vendido por categoría.</p>
            <div class="mt-4">
                <canvas id="chartRevenueByCategory" height="140"></canvas>
            </div>
        </div>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <h2 class="text-lg font-extrabold">Ventas totales ({{ $periodoLabel }})</h2>
            <p class="text-sm text-slate-600">Total vendido por día.</p>
            <div class="mt-4">
                <canvas id="chartSalesLast7Days" height="140"></canvas>
            </div>
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <h2 class="text-lg font-extrabold">Ticket promedio ({{ $periodoLabel }})</h2>
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
