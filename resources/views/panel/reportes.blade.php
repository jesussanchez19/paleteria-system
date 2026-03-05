@extends('layouts.app')
@section('title','Reportes')

@section('content')
<div class="space-y-6">
  <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
    <div>
      <h1 class="text-2xl sm:text-3xl font-extrabold">Reportes 📊</h1>
      <p class="text-slate-600">
        Período: <b>{{ $periodoLabel }}</b>
      </p>
    </div>
    <div class="flex gap-2 items-center flex-wrap">
      <a href="{{ route('reporte.diario') }}" class="px-4 py-2 rounded-xl bg-white border border-slate-200 font-bold hover:bg-slate-50 transition">
        Ver reporte diario
      </a>
      <a href="{{ route('panel.reportes.vendedores') }}" class="px-4 py-2 rounded-xl bg-white border border-slate-200 font-bold hover:bg-slate-50 transition">
        Reporte por vendedores
      </a>
      <a href="{{ route('panel.reportes.semanal') }}" class="px-4 py-2 rounded-xl bg-white border border-slate-200 font-bold hover:bg-slate-50 transition">
        Reporte semanal
      </a>
      <a href="{{ route('panel.caja.index') }}" class="px-4 py-2 rounded-xl bg-white border border-slate-200 font-bold hover:bg-slate-50 transition">
        Caja
      </a>
      <a href="{{ route('panel.index') }}" class="px-4 py-2 rounded-xl bg-white border border-slate-200 font-bold hover:bg-slate-50 transition">
        ← Volver
      </a>
    </div>
  </div>

  {{-- Selector de rango de fechas --}}
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
      <a href="{{ route('reportes.pdf', ['start_date' => $startDate ?? $today, 'end_date' => $endDate ?? $today]) }}" 
         class="px-4 py-2 bg-rose-500 text-white font-bold rounded-lg hover:bg-rose-600 transition">
        📄 Descargar PDF
      </a>
      @if(!empty($qrBase64))
      <div class="flex items-center gap-2 ml-2 p-1 bg-white border border-slate-200 rounded-lg" title="Escanea para descargar PDF">
        <img src="{{ $qrBase64 }}" alt="QR" style="width: 64px; height: 64px;">
      </div>
      @endif
    </form>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="block group relative tooltip-target">
      <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
        <p class="text-sm text-slate-600">Ventas ({{ $periodoLabel }})</p>
        <p class="text-3xl font-extrabold mt-2">
          ${{ number_format($salesByHour->sum('total'), 2) }}
        </p>
        <p class="text-xs text-slate-500 mt-2">Total vendido.</p>
      </div>
    </div>
    <div class="block group relative tooltip-target">
      <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
        <p class="text-sm text-slate-600">Tickets ({{ $periodoLabel }})</p>
        <p class="text-3xl font-extrabold mt-2">
          {{ $salesByHour->sum('qty') }}
        </p>
        <p class="text-xs text-slate-500 mt-2">Número de ventas registradas.</p>
      </div>
    </div>
    <div class="block group relative tooltip-target">
      <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
        <p class="text-sm text-slate-600">Top producto</p>
        <p class="text-xl font-extrabold mt-2">
          @if($topProducts->isNotEmpty())
            {{ $topProducts->first()->name }}
          @else
            —
          @endif
        </p>
        <p class="text-xs text-slate-500 mt-2">El más vendido.</p>
      </div>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
      <h2 class="text-lg font-extrabold">Ventas por hora ({{ $periodoLabel }})</h2>
      <div class="mt-4 relative">
        <canvas id="chartSalesByHour" height="140"></canvas>
      </div>
    </div>
    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
      <h2 class="text-lg font-extrabold">Top productos ({{ $periodoLabel }})</h2>
      <div class="mt-4 relative">
        <canvas id="chartTopProducts" height="140"></canvas>
      </div>
    </div>
  </div>

  <div class="mt-6 flex justify-center">
    <a href="{{ route('reportes.graficas', ['start_date' => $startDate ?? $today, 'end_date' => $endDate ?? $today]) }}" 
       class="px-6 py-3 rounded-xl bg-pink-500 text-white font-bold hover:bg-pink-600 transition text-lg">
      Ver todas las gráficas y detalles
    </a>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    const salesByHour = @json($salesByHour);
    const topProducts = @json($topProducts);
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
</div>
@endsection
