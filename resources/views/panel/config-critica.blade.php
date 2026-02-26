@extends('layouts.app')
@section('title','Config Crítica')

@section('content')
<div class="space-y-6">
  <div class="flex items-end justify-between gap-3">
    <div>
      <h1 class="text-2xl sm:text-3xl font-extrabold">Config Crítica 🔒</h1>
      <p class="text-slate-600">Solo Admin. Placeholder: acciones delicadas del sistema.</p>
    </div>
    <a href="{{ route('panel.index') }}" class="px-4 py-2 rounded-xl bg-white border border-slate-200 font-bold hover:bg-slate-50 transition">
      ← Volver
    </a>
    </a>
  </div>

  <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
    <h2 class="text-lg font-extrabold">Acciones críticas (placeholder)</h2>
    <ul class="mt-3 space-y-2 text-slate-700">
      <li>• Reset de configuraciones</li>
      <li>• Modo mantenimiento</li>
      <li>• Gestión avanzada (T5+)</li>
    </ul>

    <div class="mt-4 p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 font-semibold">
      Estas acciones se implementarán más adelante. Por ahora es solo UI.
    </div>
  </div>
</div>
@endsection
