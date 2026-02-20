@extends('layouts.app')
@section('title', 'Reporte diario')

@section('content')
<div class="max-w-full sm:max-w-6xl mx-auto space-y-6 px-2 sm:px-0">

    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
        <div>
            <h1 class="text-xl sm:text-3xl font-extrabold">Reporte diario 📊</h1>
            <p class="text-slate-600">Fecha: <b>{{ $date }}</b></p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('reporte.diario.pdf') }}"
               class="px-4 py-2 rounded-xl bg-slate-900 text-white font-extrabold hover:bg-slate-800 transition">
                Descargar PDF
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

    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm overflow-x-auto">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-extrabold">Resumen por producto</h2>
            <span class="text-sm text-slate-600">Productos: <b>{{ $byProduct->count() }}</b></span>
        </div>

        @if($byProduct->isEmpty())
            <p class="text-slate-600 mt-4">Hoy no hay ventas registradas.</p>
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
            <p class="text-slate-600 mt-2">Hoy no hay ventas.</p>
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
