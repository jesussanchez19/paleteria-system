@extends('layouts.app')

@section('title','Análisis del clima con IA')

@section('content')

<div class="max-w-5xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold">Análisis de Clima con IA 🌤️</h1>
            <p class="text-slate-600">Impacto del clima en tu negocio</p>
        </div>
        <a href="{{ route('panel.index') }}" class="px-4 py-2 rounded-xl bg-white border border-slate-200 font-bold hover:bg-slate-50 transition">
            ← Volver al panel
        </a>
    </div>

@if(!$weather)

    <div class="bg-amber-50 border border-amber-200 p-5 rounded-xl flex items-center gap-3">
        <span class="text-2xl">⚠️</span>
        <div>
            <p class="font-bold text-amber-800">No hay datos del clima aún</p>
            <p class="text-sm text-amber-700">El sistema sincroniza automáticamente el clima diariamente</p>
        </div>
    </div>

@else

    {{-- ============================= --}}
    {{-- ALERTAS CLIMÁTICAS --}}
    {{-- ============================= --}}
    @if(count($alertas) > 0)
    <div class="space-y-3">
        @foreach($alertas as $alerta)
        <div class="rounded-xl p-4 flex items-center gap-3 border
            {{ $alerta['tipo'] === 'danger' ? 'bg-rose-50 border-rose-200' : '' }}
            {{ $alerta['tipo'] === 'warning' ? 'bg-amber-50 border-amber-200' : '' }}
            {{ $alerta['tipo'] === 'info' ? 'bg-blue-50 border-blue-200' : '' }}
        ">
            <span class="text-2xl">{{ $alerta['icono'] }}</span>
            <div>
                <p class="font-bold 
                    {{ $alerta['tipo'] === 'danger' ? 'text-rose-800' : '' }}
                    {{ $alerta['tipo'] === 'warning' ? 'text-amber-800' : '' }}
                    {{ $alerta['tipo'] === 'info' ? 'text-blue-800' : '' }}
                ">{{ $alerta['titulo'] }}</p>
                <p class="text-sm
                    {{ $alerta['tipo'] === 'danger' ? 'text-rose-700' : '' }}
                    {{ $alerta['tipo'] === 'warning' ? 'text-amber-700' : '' }}
                    {{ $alerta['tipo'] === 'info' ? 'text-blue-700' : '' }}
                ">{{ $alerta['mensaje'] }}</p>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- ============================= --}}
    {{-- CLIMA ACTUAL + ANÁLISIS IA --}}
    {{-- ============================= --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        {{-- Clima actual --}}
        <div class="bg-gradient-to-br from-sky-500 to-blue-600 rounded-2xl p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-80">Clima actual</p>
                    <p class="text-5xl font-extrabold mt-2">{{ $weather->temp }}°C</p>
                    <p class="text-lg mt-2 opacity-90">{{ $weather->condition }}</p>
                    @if($weather->humidity)
                    <p class="text-sm mt-2 opacity-70">💧 Humedad: {{ $weather->humidity }}%</p>
                    @endif
                </div>
                @if($weather->icon)
                <img src="https://openweathermap.org/img/wn/{{ $weather->icon }}@2x.png" 
                     alt="{{ $weather->condition }}" 
                     class="w-24 h-24 opacity-90">
                @else
                <span class="text-7xl opacity-80">
                    @if($weather->temp >= 30) ☀️
                    @elseif($weather->temp >= 25) 🌤️
                    @elseif($weather->temp >= 20) ⛅
                    @else 🌥️
                    @endif
                </span>
                @endif
            </div>
            <div class="mt-4 pt-4 border-t border-white/20">
                <p class="text-sm">{{ $recommendation }}</p>
            </div>
        </div>

        {{-- Análisis IA --}}
        <div class="bg-gradient-to-br from-violet-600 to-indigo-700 rounded-2xl p-6 text-white shadow-lg">
            <div class="flex items-start gap-3">
                <span class="text-3xl">🤖</span>
                <div>
                    <h2 class="font-extrabold text-lg mb-2">Análisis IA del Clima</h2>
                    <p class="text-white/90 leading-relaxed text-sm">{{ $analisisIA }}</p>
                </div>
            </div>
            @if($correlacion['tipo'] !== 'sin_datos')
            <div class="mt-4 pt-4 border-t border-white/20">
                <p class="text-sm text-white/80">
                    📊 Correlación detectada: 
                    <span class="font-bold text-white">{{ $correlacion['mensaje'] }}</span>
                    @if(isset($correlacion['porcentaje']))
                    ({{ $correlacion['porcentaje'] }}%)
                    @endif
                </p>
            </div>
            @endif
        </div>
    </div>

    {{-- ============================= --}}
    {{-- RECOMENDACIONES + PREGUNTA RÁPIDA --}}
    {{-- ============================= --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        {{-- Recomendaciones de producción --}}
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <h2 class="font-extrabold text-slate-800 mb-4">📋 Recomendaciones de Producción</h2>
            @if(count($recomendacionesProduccion) > 0)
            <div class="space-y-3">
                @foreach($recomendacionesProduccion as $rec)
                <div class="flex items-center gap-3 p-3 rounded-xl
                    {{ $rec['prioridad'] === 'alta' ? 'bg-rose-50 border border-rose-200' : '' }}
                    {{ $rec['prioridad'] === 'media' ? 'bg-amber-50 border border-amber-200' : '' }}
                    {{ $rec['prioridad'] === 'baja' ? 'bg-slate-50 border border-slate-200' : '' }}
                ">
                    <span class="text-2xl">{{ $rec['icono'] }}</span>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-slate-700">{{ $rec['texto'] }}</p>
                    </div>
                    <span class="text-xs px-2 py-1 rounded-full
                        {{ $rec['prioridad'] === 'alta' ? 'bg-rose-500 text-white' : '' }}
                        {{ $rec['prioridad'] === 'media' ? 'bg-amber-500 text-white' : '' }}
                        {{ $rec['prioridad'] === 'baja' ? 'bg-slate-400 text-white' : '' }}
                    ">{{ ucfirst($rec['prioridad']) }}</span>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-slate-500 text-sm">Clima templado - producción normal recomendada</p>
            @endif
        </div>

        {{-- Pregunta rápida --}}
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <h2 class="font-extrabold text-slate-800 mb-4">💬 Pregunta sobre el Clima</h2>
            <form id="clima-ask-form" class="space-y-3">
                @csrf
                <input type="text" 
                    id="clima-question" 
                    placeholder="Ej: ¿Cuánto vendí el día más caluroso?" 
                    class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    maxlength="200">
                <button type="submit" 
                    id="clima-ask-btn"
                    class="w-full px-4 py-3 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 transition disabled:opacity-50">
                    Preguntar a la IA
                </button>
            </form>
            <div id="clima-answer" class="mt-4 hidden">
                <div class="p-4 bg-blue-50 border border-blue-200 rounded-xl">
                    <p class="text-sm text-blue-800" id="clima-answer-text"></p>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================= --}}
    {{-- HISTORIAL CLIMA VS VENTAS --}}
    {{-- ============================= --}}
    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
        <h2 class="font-extrabold text-slate-800 mb-4">📅 Historial: Clima vs Ventas (7 días)</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-200">
                        <th class="text-left py-2 font-bold text-slate-600">Fecha</th>
                        <th class="text-center py-2 font-bold text-slate-600">Temp</th>
                        <th class="text-center py-2 font-bold text-slate-600">Condición</th>
                        <th class="text-right py-2 font-bold text-slate-600">Ventas</th>
                        <th class="text-center py-2 font-bold text-slate-600">Correlación</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($historialClima as $dia)
                    <tr class="border-b border-slate-100 hover:bg-slate-50">
                        <td class="py-3">
                            {{ \Carbon\Carbon::parse($dia['date'])->locale('es')->isoFormat('ddd D MMM') }}
                            @if($dia['date'] === now()->toDateString())
                            <span class="ml-2 text-xs px-2 py-0.5 bg-emerald-100 text-emerald-700 rounded-full">Hoy</span>
                            @endif
                        </td>
                        <td class="py-3 text-center">
                            <span class="font-bold {{ $dia['temp'] >= 28 ? 'text-orange-600' : ($dia['temp'] <= 20 ? 'text-blue-600' : 'text-slate-700') }}">
                                {{ $dia['temp'] }}°C
                            </span>
                        </td>
                        <td class="py-3 text-center text-slate-600">{{ $dia['condition'] }}</td>
                        <td class="py-3 text-right font-bold">${{ number_format($dia['ventas'], 0) }}</td>
                        <td class="py-3 text-center">
                            @if($dia['temp'] >= 28 && $dia['ventas'] > 500)
                            <span class="text-emerald-600">↑ Alta</span>
                            @elseif($dia['temp'] <= 20 && $dia['ventas'] < 300)
                            <span class="text-blue-600">↓ Baja</span>
                            @else
                            <span class="text-slate-400">— Normal</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-4 text-center text-slate-500">Sin datos históricos</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endif

</div>

<script>
// Pregunta rápida sobre el clima
document.getElementById('clima-ask-form')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const question = document.getElementById('clima-question').value.trim();
    if (!question) return;
    
    const btn = document.getElementById('clima-ask-btn');
    const answerDiv = document.getElementById('clima-answer');
    const answerText = document.getElementById('clima-answer-text');
    
    btn.disabled = true;
    btn.textContent = 'Analizando...';
    answerDiv.classList.add('hidden');
    
    try {
        const response = await fetch('{{ route("panel.weather.ask") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ question })
        });
        
        const data = await response.json();
        
        if (data.success) {
            answerText.textContent = data.answer;
            answerDiv.classList.remove('hidden');
        } else {
            answerText.textContent = 'Error al procesar la pregunta';
            answerDiv.classList.remove('hidden');
        }
    } catch (error) {
        answerText.textContent = 'Error de conexión';
        answerDiv.classList.remove('hidden');
    } finally {
        btn.disabled = false;
        btn.textContent = 'Preguntar a la IA';
    }
});
</script>

@endsection
