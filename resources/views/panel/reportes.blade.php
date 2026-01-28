@extends('layouts.app')
@section('title','Reportes')

@section('content')
<div class="space-y-6">
  <div class="flex items-end justify-between gap-3">
    <div>
      <h1 class="text-2xl sm:text-3xl font-extrabold">Reportes 📊</h1>
      <p class="text-slate-600">Placeholder: aquí mostraremos ventas del día, top productos y gráficos.</p>
    </div>
    <a href="{{ route('panel.index') }}" class="px-4 py-2 rounded-xl bg-white border border-slate-200 font-bold hover:bg-slate-50 transition">
      ← Panel
    </a>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
      <p class="text-sm text-slate-600">Ventas del día</p>
      <p class="text-3xl font-extrabold mt-2">$0.00</p>
      <p class="text-xs text-slate-500 mt-2">En T4 agregamos datos reales.</p>
    </div>

    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
      <p class="text-sm text-slate-600">Tickets / ventas</p>
      <p class="text-3xl font-extrabold mt-2">0</p>
      <p class="text-xs text-slate-500 mt-2">En T4 conectamos ventas guardadas.</p>
    </div>

    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
      <p class="text-sm text-slate-600">Top producto</p>
      <p class="text-xl font-extrabold mt-2">—</p>
      <p class="text-xs text-slate-500 mt-2">En T4 calculamos automáticamente.</p>
    </div>
  </div>

  <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
    <h2 class="text-lg font-extrabold">Gráficas (placeholder)</h2>
    <div class="mt-4 h-48 rounded-2xl border border-dashed border-slate-300 flex items-center justify-center text-slate-500 font-semibold">
      Aquí irá la gráfica del día
    </div>
  </div>
</div>
@endsection
