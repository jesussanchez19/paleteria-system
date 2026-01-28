@extends('layouts.app')
@section('title','Configuración')

@section('content')
<div class="space-y-6">
  <div class="flex items-end justify-between gap-3">
    <div>
      <h1 class="text-2xl sm:text-3xl font-extrabold">Configuración ⚙️</h1>
      <p class="text-slate-600">Placeholder: UI operativa (sin guardar todavía).</p>
    </div>
    <a href="{{ route('panel.index') }}" class="px-4 py-2 rounded-xl bg-white border border-slate-200 font-bold hover:bg-slate-50 transition">
      ← Panel
    </a>
  </div>

  <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
    <h2 class="text-lg font-extrabold mb-4">Parámetros del negocio</h2>

    <form onsubmit="event.preventDefault();" class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="text-sm font-bold text-slate-700">Nombre de la paletería</label>
        <input class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2" placeholder="Paletería..." disabled>
      </div>

      <div>
        <label class="text-sm font-bold text-slate-700">Ubicación (para clima)</label>
        <input class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2" placeholder="Ciudad / CP" disabled>
      </div>

      <div>
        <label class="text-sm font-bold text-slate-700">Horario</label>
        <input class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2" placeholder="9:00 - 20:00" disabled>
      </div>

      <div>
        <label class="text-sm font-bold text-slate-700">Stock mínimo (alerta)</label>
        <input class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2" placeholder="Ej. 10" disabled>
      </div>

      <div class="md:col-span-2">
        <button class="px-5 py-3 rounded-2xl bg-slate-900 text-white font-extrabold opacity-50 cursor-not-allowed" disabled>
          Guardar (T4)
        </button>
        <p class="text-xs text-slate-500 mt-2">Se guardará en T4 (tabla settings).</p>
      </div>
    </form>
  </div>
</div>
@endsection
