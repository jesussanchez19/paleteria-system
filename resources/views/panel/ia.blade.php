@extends('layouts.app')
@section('title','Asistente IA')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <div class="flex items-end justify-between gap-3">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold">Asistente IA 🤖</h1>
            <p class="text-slate-600">Haz preguntas sobre ventas, inventario y tendencias</p>
        </div>
        <a href="{{ route('panel.index') }}" class="px-4 py-2 rounded-xl bg-white border border-slate-200 font-bold hover:bg-slate-50 transition">
            ← Volver
        </a>
    </div>

    {{-- Formulario de pregunta --}}
    <form method="POST" action="{{ route('panel.ia.ask') }}" class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
        @csrf
        <div class="flex flex-col sm:flex-row gap-3">
            <input
                type="text"
                name="question"
                placeholder="Ej: ¿Cuál es el producto más vendido?"
                value="{{ old('question') }}"
                class="flex-1 rounded-xl border border-slate-200 px-4 py-3 focus:ring-2 focus:ring-pink-300 focus:border-pink-400"
                required
                autofocus
            >
            <button type="submit" class="px-6 py-3 rounded-xl bg-slate-900 text-white font-extrabold hover:bg-slate-800 transition">
                🔍 Preguntar
            </button>
        </div>
        @error('question')
            <p class="text-sm text-rose-600 mt-2">{{ $message }}</p>
        @enderror
    </form>

    {{-- Respuesta de IA --}}
    @if(session('answer'))
    <div class="bg-gradient-to-r from-emerald-50 to-teal-50 border border-emerald-200 rounded-2xl p-5 shadow-sm">
        <div class="flex items-start gap-3">
            <span class="text-2xl">🤖</span>
            <div class="flex-1">
                <p class="font-bold text-emerald-800 mb-2">Respuesta del Asistente</p>
                <div class="text-slate-700 whitespace-pre-line">
                    {!! nl2br(e(session('answer'))) !!}
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Sugerencias rápidas --}}
    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
        <h2 class="font-extrabold text-slate-800 mb-4">💡 Preguntas sugeridas</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
            @php
                $sugerencias = [
                    '📊 ventas hoy',
                    '🏆 producto más vendido',
                    '📉 menos vendido',
                    '⚠️ inventario bajo',
                    '🚨 sin stock',
                    '📦 sugerencia de reposición',
                    '🔥 tendencias',
                    '📅 mejor día',
                    '📋 resumen',
                    '🏷️ categoría popular',
                    '🧾 ticket promedio',
                    '❓ ayuda',
                ];
            @endphp
            @foreach($sugerencias as $sug)
                <button type="button" 
                        onclick="document.querySelector('input[name=question]').value = '{{ str_replace(['📊 ', '🏆 ', '📉 ', '⚠️ ', '🚨 ', '📦 ', '🔥 ', '📅 ', '📋 ', '🏷️ ', '🧾 ', '❓ '], '', $sug) }}'"
                        class="text-left px-3 py-2 rounded-lg bg-slate-50 hover:bg-slate-100 border border-slate-200 text-sm font-medium text-slate-700 transition">
                    {{ $sug }}
                </button>
            @endforeach
        </div>
    </div>

    {{-- Info adicional --}}
    <div class="text-center text-sm text-slate-500">
        <p>El asistente analiza datos reales del sistema para darte información útil sobre tu negocio.</p>
    </div>

</div>
@endsection
