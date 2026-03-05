@extends('layouts.app')

@section('title','Clima')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold">Clima ☀️🌧️</h1>
            <p class="text-slate-600">Ciudad: <b>{{ $city }}</b> • Fecha: <b>{{ $today }}</b></p>
        </div>
        <a href="{{ route('panel.index') }}"
           class="px-4 py-2 rounded-xl bg-white border border-slate-200 font-bold hover:bg-slate-50 transition">
            ← Volver al panel
        </a>
    </div>

    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
        @if(!$snap)
            <div class="text-center py-8">
                <p class="text-4xl mb-4">🌤️</p>
                <p class="text-slate-600 font-medium">
                    Aún no hay snapshot de hoy.
                </p>
                <p class="text-xs text-slate-500 mt-2">
                    Ejecuta: <code class="bg-slate-100 px-2 py-1 rounded">php artisan weather:snapshot</code>
                </p>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div class="text-center sm:text-left">
                    <p class="text-xs text-slate-500 uppercase tracking-wide mb-1">Temperatura</p>
                    <p class="text-4xl font-extrabold text-pink-600">{{ number_format((float)$snap->temp, 1) }}°C</p>
                </div>
                <div class="sm:col-span-2 text-center sm:text-left">
                    <p class="text-xs text-slate-500 uppercase tracking-wide mb-1">Condición</p>
                    <p class="text-2xl font-bold capitalize">{{ $snap->condition }}</p>
                    <p class="text-xs text-slate-400 mt-2">
                        Actualizado: {{ $snap->updated_at->format('H:i') }}
                    </p>
                </div>
            </div>

            {{-- Indicador visual --}}
            <div class="mt-6 pt-6 border-t border-slate-100">
                <div class="flex items-center gap-4">
                    @php
                        $temp = (float) $snap->temp;
                        $icon = $temp >= 30 ? '🔥' : ($temp >= 25 ? '☀️' : ($temp >= 18 ? '🌤️' : ($temp >= 10 ? '🌥️' : '❄️')));
                        $color = $temp >= 30 ? 'text-red-500' : ($temp >= 25 ? 'text-orange-500' : ($temp >= 18 ? 'text-yellow-500' : ($temp >= 10 ? 'text-blue-400' : 'text-blue-600')));
                    @endphp
                    <span class="text-5xl">{{ $icon }}</span>
                    <div>
                        <p class="font-bold {{ $color }}">
                            @if($temp >= 30)
                                ¡Día muy caluroso! Buenas ventas de paletas y aguas.
                            @elseif($temp >= 25)
                                Día cálido. Excelente para ventas de helados.
                            @elseif($temp >= 18)
                                Temperatura agradable.
                            @elseif($temp >= 10)
                                Día fresco.
                            @else
                                Día frío. Considera productos calientes.
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        @endif
    </div>

</div>
@endsection
