@extends('layouts.app')
@section('title','Reportes')

@section('content')
<div class="space-y-6">
  <div class="flex items-end justify-between gap-3">
    <div>
      <h1 class="text-2xl sm:text-3xl font-extrabold">Reportes 📊</h1>
      <p class="text-slate-600">Placeholder: aquí mostraremos ventas del día, top productos y gráficos.</p>
    </div>
    <div class="flex gap-2 items-center">
      <a href="{{ route('reporte.diario') }}" class="px-4 py-2 rounded-xl bg-white border border-slate-200 font-bold hover:bg-slate-50 transition">
        Ver reporte diario
      </a>
      <a href="{{ route('panel.index') }}" class="px-4 py-2 rounded-xl bg-white border border-slate-200 font-bold hover:bg-slate-50 transition">
        ← Volver
      </a>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <a href="{{ route('reporte.diario') }}" class="block group relative tooltip-target">
      <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm group-hover:shadow-md transition cursor-pointer relative">
        <p class="text-sm text-slate-600">Ventas del día</p>
        <p class="text-3xl font-extrabold mt-2">
          ${{ number_format($salesByHour->sum('total'), 2) }}
        </p>
        <p class="text-xs text-slate-500 mt-2">Total vendido hoy.</p>
        <div id="tooltip-ventas" class="hidden pointer-events-none fixed px-3 py-1 rounded bg-black text-white text-xs font-bold z-50">Más detalles</div>
      </div>
    </a>
    <a href="{{ route('reportes.graficas') }}" class="block group relative tooltip-target">
      <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm group-hover:shadow-md transition cursor-pointer relative">
        <p class="text-sm text-slate-600">Tickets / ventas</p>
        <p class="text-3xl font-extrabold mt-2">
          {{ $salesByHour->sum('qty') }}
        </p>
        <p class="text-xs text-slate-500 mt-2">Número de ventas registradas hoy.</p>
        <div id="tooltip-tickets" class="hidden pointer-events-none fixed px-3 py-1 rounded bg-black text-white text-xs font-bold z-50">Más detalles</div>
      </div>
    </a>
    <a href="{{ route('reportes.graficas') }}" class="block group relative tooltip-target">
      <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm group-hover:shadow-md transition cursor-pointer relative">
        <p class="text-sm text-slate-600">Top producto</p>
        <p class="text-xl font-extrabold mt-2">
          @if($topProducts->isNotEmpty())
            {{ $topProducts->first()->name }}
          @else
            —
          @endif
        </p>
        <p class="text-xs text-slate-500 mt-2">El más vendido hoy.</p>
        <div id="tooltip-top" class="hidden pointer-events-none fixed px-3 py-1 rounded bg-black text-white text-xs font-bold z-50">Más detalles</div>
      </div>
    </a>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
    <a href="{{ route('reportes.graficas') }}" class="block group relative">
      <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm group-hover:shadow-md transition cursor-pointer relative">
        <h2 class="text-lg font-extrabold">Ventas por hora</h2>
        <div class="mt-4 relative">
          <canvas id="chartSalesByHour" height="140" style="z-index:1;"></canvas>
          <div id="tooltip-sales" class="hidden pointer-events-none fixed px-3 py-1 rounded bg-black text-white text-xs font-bold z-50">Más detalles</div>
        </div>
      </div>
    </a>
    <a href="{{ route('reportes.graficas') }}" class="block group relative">
      <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm group-hover:shadow-md transition cursor-pointer relative">
        <h2 class="text-lg font-extrabold">Top productos (cantidad)</h2>
        <div class="mt-4 relative">
          <canvas id="chartTopProducts" height="140" style="z-index:1;"></canvas>
          <div id="tooltip-products" class="hidden pointer-events-none fixed px-3 py-1 rounded bg-black text-white text-xs font-bold z-50">Más detalles</div>
        </div>
      </div>
    </a>
  </div>
  <div class="text-xs text-slate-400 mt-2">Haz clic en la gráfica para ver el detalle.</div>

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

    // Tooltip personalizado para cada gráfica
    function setupTooltip(canvasId, tooltipId) {
      const canvas = document.getElementById(canvasId);
      const tooltip = document.getElementById(tooltipId);
      canvas.addEventListener('mousemove', function(e) {
        tooltip.style.left = (e.clientX + 12) + 'px';
        tooltip.style.top = (e.clientY + 12) + 'px';
        tooltip.classList.remove('hidden');
      });
      canvas.addEventListener('mouseleave', function() {
        tooltip.classList.add('hidden');
      });
    }
    setupTooltip('chartSalesByHour', 'tooltip-sales');
    setupTooltip('chartTopProducts', 'tooltip-products');

    // Tooltip flotante para cada tarjeta del resumen
    function setupTooltipCard(cardSelector, tooltipId) {
      const cards = document.querySelectorAll(cardSelector);
      cards.forEach(function(card) {
        const tooltip = card.querySelector('#' + tooltipId);
        if (!tooltip) return;
        card.addEventListener('mousemove', function(e) {
          tooltip.style.left = (e.clientX + 12) + 'px';
          tooltip.style.top = (e.clientY + 12) + 'px';
          tooltip.classList.remove('hidden');
        });
        card.addEventListener('mouseleave', function() {
          tooltip.classList.add('hidden');
        });
      });
    }
    setupTooltipCard('.tooltip-target', 'tooltip-ventas');
    setupTooltipCard('.tooltip-target', 'tooltip-tickets');
    setupTooltipCard('.tooltip-target', 'tooltip-top');
  </script>
</div>
@endsection
