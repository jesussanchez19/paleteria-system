@extends('layouts.app')
@section('title', 'Reporte diario')

@section('content')
<div class="max-w-full sm:max-w-6xl mx-auto space-y-6 px-2 sm:px-0">

    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
        <div>
            <h1 class="text-xl sm:text-3xl font-extrabold">Reporte diario 📊</h1>
            <p class="text-slate-600">Fecha: <b>{{ $date === now()->toDateString() ? 'Hoy (' . \Carbon\Carbon::parse($date)->format('d/m/Y') . ')' : \Carbon\Carbon::parse($date)->format('d/m/Y') }}</b></p>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            <form method="GET" action="{{ route('reporte.diario') }}" class="flex items-center gap-2">
                <input type="date" name="date" value="{{ $date }}" 
                       class="rounded-xl border border-slate-200 px-3 py-2 text-sm">
                <button type="submit" 
                        class="px-4 py-2 rounded-xl bg-slate-900 text-white font-bold hover:bg-slate-800 transition">
                    Filtrar
                </button>
            </form>
            <a href="{{ route('reporte.diario.pdf', ['date' => $date]) }}"
               class="px-4 py-2 rounded-xl bg-slate-900 text-white font-extrabold hover:bg-slate-800 transition">
                Descargar PDF
            </a>
            <a href="{{ route('panel.reportes.vendedores', ['date' => $date]) }}"
               class="px-4 py-2 rounded-xl bg-white border border-slate-200 font-bold text-slate-800 hover:bg-slate-50 transition">
                Por vendedores
            </a>
            <a href="{{ route('panel.reportes.semanal') }}"
               class="px-4 py-2 rounded-xl bg-white border border-slate-200 font-bold text-slate-800 hover:bg-slate-50 transition">
                Semanal
            </a>
            <a href="{{ route('reportes.index') }}"
               class="px-4 py-2 rounded-xl bg-white border border-slate-200 font-bold text-slate-800 hover:bg-slate-50 transition">
                ← Volver
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <p class="text-xs sm:text-sm text-slate-600">Ventas del día</p>
            <p class="text-2xl sm:text-3xl font-extrabold">{{ $salesCount }}</p>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <p class="text-xs sm:text-sm text-slate-600">Total del día</p>
            <p class="text-2xl sm:text-3xl font-extrabold">${{ number_format($total, 2) }}</p>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm flex flex-col items-center justify-center gap-2">
            <p class="text-xs sm:text-sm text-slate-600">QR del reporte</p>
            <img src="{{ $qrUrl }}" class="w-20 h-20 sm:w-24 sm:h-24 rounded-xl border border-slate-200 mx-auto" alt="QR">
        </div>
    </div>

    {{-- Sección de Corte de Caja --}}
    @if($cashData)
    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-extrabold">💰 Corte de Caja</h2>
            @if($cashData['is_closed'] && $cashData['has_real_amount'])
                <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-700 font-bold text-sm">Cerrada</span>
            @elseif($cashData['is_closed'] && !$cashData['has_real_amount'])
                <span class="px-3 py-1 rounded-full bg-blue-100 text-blue-700 font-bold text-sm">Pendiente</span>
            @else
                <span class="px-3 py-1 rounded-full bg-amber-100 text-amber-700 font-bold text-sm">Abierta</span>
            @endif
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-4">
            <div class="bg-slate-50 rounded-xl p-3">
                <p class="text-xs text-slate-600">Apertura</p>
                <p class="text-lg font-bold">${{ number_format($cashData['opening_amount'], 2) }}</p>
                <p class="text-xs text-slate-500">{{ \Carbon\Carbon::parse($cashData['opened_at'])->format('H:i') }}</p>
            </div>
            <div class="bg-slate-50 rounded-xl p-3">
                <p class="text-xs text-slate-600">Ventas del turno</p>
                <p class="text-lg font-bold">${{ number_format($cashData['sales_during_shift'], 2) }}</p>
            </div>
            <div class="bg-blue-50 rounded-xl p-3">
                <p class="text-xs text-blue-600">Esperado en caja</p>
                <p class="text-lg font-extrabold text-blue-700">${{ number_format($cashData['expected_amount'], 2) }}</p>
            </div>
            @if($cashData['is_closed'] && $cashData['has_real_amount'])
            <div class="{{ $cashData['difference'] >= 0 ? 'bg-emerald-50' : 'bg-rose-50' }} rounded-xl p-3">
                <p class="text-xs {{ $cashData['difference'] >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">Diferencia</p>
                <p class="text-lg font-extrabold {{ $cashData['difference'] >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                    {{ $cashData['difference'] >= 0 ? '+' : '' }}${{ number_format($cashData['difference'], 2) }}
                </p>
                <p class="text-xs text-slate-500">Cierre: {{ \Carbon\Carbon::parse($cashData['closed_at'])->format('H:i') }}</p>
            </div>
            @elseif($cashData['is_closed'] && !$cashData['has_real_amount'])
            <div class="bg-amber-50 rounded-xl p-3">
                <p class="text-xs text-amber-600">Cierre real</p>
                <p class="text-lg font-bold text-amber-700">Pendiente</p>
                <p class="text-xs text-slate-500">{{ \Carbon\Carbon::parse($cashData['closed_at'])->format('H:i') }}</p>
            </div>
            @endif
        </div>

        @if($cashData['is_closed'] && $cashData['has_real_amount'])
            {{-- Caja cerrada con dinero registrado --}}
            <div class="bg-emerald-50 rounded-xl p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-600">Dinero real reportado:</p>
                        <p class="text-2xl font-extrabold">${{ number_format((float)$cashData['closing_amount'], 2) }}</p>
                    </div>
                    <div class="text-right">
                        @if($cashData['difference'] == 0)
                            <span class="text-emerald-600 font-bold">✓ Cuadra perfecto</span>
                        @elseif($cashData['difference'] > 0)
                            <span class="text-emerald-600 font-bold">↑ Sobrante</span>
                        @else
                            <span class="text-rose-600 font-bold">↓ Faltante</span>
                        @endif
                    </div>
                </div>
            </div>
        @elseif($cashData['is_closed'] && !$cashData['has_real_amount'])
            {{-- Caja cerrada pero pendiente de dinero real --}}
            <div class="bg-blue-50 rounded-xl p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-700 font-bold">💵 Pendiente de registrar dinero real</p>
                        <p class="text-sm text-slate-600">El gerente debe ingresar el dinero contado en la sección de Caja.</p>
                    </div>
                    <a href="{{ route('panel.caja.index') }}" 
                       class="px-4 py-2 rounded-xl bg-blue-500 text-white font-bold hover:bg-blue-600 transition">
                        Ir a Caja →
                    </a>
                </div>
            </div>
        @elseif(!$cashData['is_closed'])
            {{-- Caja abierta --}}
            <div class="bg-amber-50 rounded-xl p-4">
                <div class="flex items-center gap-3">
                    <span class="text-2xl">⏰</span>
                    <div>
                        <p class="text-amber-700 font-bold">Caja abierta</p>
                        <p class="text-sm text-slate-600">El cierre automático es a las 5:00pm</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
    @else
    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
        <h2 class="text-lg font-extrabold">💰 Corte de Caja</h2>
        <p class="text-slate-600 mt-2">No hay registro de caja para este día.</p>
    </div>
    @endif

    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm overflow-x-auto">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-extrabold">Resumen por producto</h2>
            <span class="text-sm text-slate-600">Productos: <b>{{ $byProduct->count() }}</b></span>
        </div>

        @if($byProduct->isEmpty())
            <p class="text-slate-600 mt-4">{{ $date === now()->toDateString() ? 'Hoy' : \Carbon\Carbon::parse($date)->format('d/m/Y') }} no hay ventas registradas.</p>
        @else
            <div class="mt-4 overflow-x-auto">
                <table class="min-w-full text-xs sm:text-sm">
                    <thead>
                        <tr class="text-left text-slate-600 border-b">
                            <th class="py-2 pr-4">Producto</th>
                            <th class="py-2 pr-4">Cantidad</th>
                            <th class="py-2 pr-4 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($byProduct as $row)
                            <tr class="border-b last:border-b-0">
                                <td class="py-3 pr-4 font-bold">{{ $row->name }}</td>
                                <td class="py-3 pr-4">{{ (int)$row->qty }}</td>
                                <td class="py-3 pr-4 text-right font-extrabold">${{ number_format((float)$row->total, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm overflow-x-auto">
        <h2 class="text-lg font-extrabold">Ventas (detalle)</h2>

        @if($sales->isEmpty())
            <p class="text-slate-600 mt-2">{{ $date === now()->toDateString() ? 'Hoy' : 'El ' . \Carbon\Carbon::parse($date)->format('d/m/Y') }} no hay ventas.</p>
        @else
            <div class="mt-3 overflow-x-auto">
                <table class="min-w-full text-xs sm:text-sm">
                    <thead>
                        <tr class="text-left text-slate-600 border-b">
                            <th class="py-2 pr-4">Hora</th>
                            <th class="py-2 pr-4">Vendedor</th>
                            <th class="py-2 pr-4 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sales as $s)
                            <tr class="border-b last:border-b-0">
                                <td class="py-3 pr-4">{{ $s->created_at->format('H:i') }}</td>
                                <td class="py-3 pr-4">{{ $s->user->name ?? '—' }}</td>
                                <td class="py-3 pr-4 text-right font-extrabold">${{ number_format((float)$s->total, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>
@endsection
