@extends('layouts.app')
@section('title','IA')

@section('content')
<div class="space-y-6">
  <div class="flex items-end justify-between gap-3">
    <div>
      <h1 class="text-2xl sm:text-3xl font-extrabold">Asistente IA 🤖</h1>
      <p class="text-slate-600">Placeholder: UI del chat. En T5 conectaremos API (Gemini/Clima).</p>
    </div>
    <a href="{{ route('panel.index') }}" class="px-4 py-2 rounded-xl bg-white border border-slate-200 font-bold hover:bg-slate-50 transition">
      ← Volver
    </a>
    </a>
  </div>

  <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
    <div class="p-4 border-b border-slate-200 flex items-center justify-between">
      <div>
        <p class="font-extrabold">Chat de negocio</p>
        <p class="text-xs text-slate-500">Ejemplos: “¿Qué mercancía traer mañana?”</p>
      </div>
      <span class="text-xs font-bold px-2 py-1 rounded-full bg-slate-100 border border-slate-200 text-slate-700">
        Sin conexión aún
      </span>
    </div>

    <div class="p-4 space-y-3 h-80 overflow-y-auto bg-slate-50">
      <div class="max-w-[85%] bg-white border border-slate-200 rounded-2xl p-3">
        <p class="text-sm font-bold">Sistema</p>
        <p class="text-sm text-slate-600">Hola, soy tu asistente. (Placeholder)</p>
      </div>

      <div class="max-w-[85%] ml-auto bg-slate-900 text-white rounded-2xl p-3">
        <p class="text-sm font-bold">Tú</p>
        <p class="text-sm opacity-90">¿Qué mercancía debería traer mañana?</p>
      </div>

      <div class="max-w-[85%] bg-white border border-slate-200 rounded-2xl p-3">
        <p class="text-sm font-bold">Sistema</p>
        <p class="text-sm text-slate-600">
          Respuesta simulada: revisa ventas + clima. (Se implementa en T5)
        </p>
      </div>
    </div>

    <div class="p-4 border-t border-slate-200">
      <form onsubmit="event.preventDefault();" class="flex gap-2">
        <input
          class="flex-1 rounded-2xl border border-slate-200 px-4 py-3"
          placeholder="Escribe tu pregunta (aún no envía)..."
          disabled
        >
        <button class="px-5 py-3 rounded-2xl bg-slate-900 text-white font-extrabold opacity-50 cursor-not-allowed" disabled>
          Enviar
        </button>
      </form>
      <p class="text-xs text-slate-500 mt-2">Activaremos el envío cuando conectemos la IA.</p>
    </div>
  </div>
</div>
@endsection
