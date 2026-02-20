@extends('layouts.app')
@section('title', 'Gráficas del día')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold">Gráficas del día 📈</h1>
            <p class="text-slate-600">Fecha: <b>{{ $today }}</b></p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ url()->previous() }}"
               class="px-6 py-3 rounded-xl bg-pink-500 text-white font-bold hover:bg-pink-600 transition text-lg">
                ← Volver
            </a>

        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <h2 class="text-lg font-extrabold">Ventas por hora</h2>
            <p class="text-sm text-slate-600">Cantidad de ventas registradas por hora.</p>
            <div class="mt-4">
                <canvas id="chartSalesByHour" height="140"></canvas>
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <h2 class="text-lg font-extrabold">Top productos (cantidad)</h2>
            <p class="text-sm text-slate-600">Los 10 productos más vendidos hoy.</p>
            <div class="mt-4">
                <canvas id="chartTopProducts" height="140"></canvas>
            </div>
        </div>
    </div>

    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
        <h2 class="text-lg font-extrabold">Tabla rápida (Top productos)</h2>

        @if($topProducts->isEmpty())
            <p class="text-slate-600 mt-3">Hoy no hay ventas para graficar.</p>
        @else
            <div class="mt-3 overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="text-left text-slate-600 border-b">
                            <th class="py-2 pr-4">Producto</th>
                            <th class="py-2 pr-4">Cantidad</th>
                            <th class="py-2 pr-4 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($topProducts as $p)
                            <tr class="border-b last:border-b-0">
                                <td class="py-3 pr-4 font-bold">{{ $p->name }}</td>
                                <td class="py-3 pr-4">{{ (int)$p->qty }}</td>
                                <td class="py-3 pr-4 text-right font-extrabold">${{ number_format((float)$p->total, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Datos desde PHP → JS
    const salesByHour = @json($salesByHour);
    const topProducts = @json($topProducts);

    // Ventas por hora
    const hourLabels = salesByHour.map(x => x.hour + ':00');
    const hourQty = salesByHour.map(x => Number(x.qty));

    new Chart(document.getElementById('chartSalesByHour'), {
        type: 'line',
        data: {
            labels: hourLabels,
            datasets: [{
                label: 'Ventas (cantidad)',
                data: hourQty,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: true } },
            scales: { y: { beginAtZero: true, precision: 0 } }
        }
    });

    // Top productos por cantidad
    const prodLabels = topProducts.map(x => x.name);
    const prodQty = topProducts.map(x => Number(x.qty));

    new Chart(document.getElementById('chartTopProducts'), {
        type: 'bar',
        data: {
            labels: prodLabels,
            datasets: [{
                label: 'Cantidad vendida',
                data: prodQty
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: true } },
            scales: { y: { beginAtZero: true, precision: 0 } }
        }
    });
</script>
@endsection
