@extends('layouts.app')

@section('title', 'Reporte semanal')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold">Reporte semanal 📆</h1>
            <p class="text-slate-600">
                Semana actual (Lunes a hoy): <b>{{ $start->format('d/m/Y') }}</b> — <b>{{ $end->format('d/m/Y') }}</b>
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('reportes.index') }}"
               class="px-4 py-2 rounded-xl bg-white border border-slate-200 font-bold hover:bg-slate-50 transition">
                ← Volver a reportes
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <p class="text-sm text-slate-600">Ventas</p>
            <p class="text-2xl font-extrabold">{{ (int)$summary->num_sales }}</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <p class="text-sm text-slate-600">Total vendido</p>
            <p class="text-2xl font-extrabold">${{ number_format((float)$summary->total_sales,2) }}</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <p class="text-sm text-slate-600">Ticket promedio</p>
            <p class="text-2xl font-extrabold">${{ number_format((float)$summary->avg_ticket,2) }}</p>
        </div>
    </div>

    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-extrabold">Top productos (semana actual)</h2>
            <span class="text-sm text-slate-600">Top 10</span>
        </div>

        @if($topProducts->isEmpty())
            <p class="text-slate-600 mt-3">No hay ventas esta semana.</p>
        @else
            <div class="mt-4 overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="text-left text-slate-600 border-b">
                            <th class="py-2 pr-4">Producto</th>
                            <th class="py-2 pr-4 text-right">Cantidad</th>
                            <th class="py-2 pr-4 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($topProducts as $p)
                            <tr class="border-b last:border-b-0">
                                <td class="py-3 pr-4 font-bold">{{ $p->name }}</td>
                                <td class="py-3 pr-4 text-right">{{ (int)$p->qty }}</td>
                                <td class="py-3 pr-4 text-right font-extrabold">${{ number_format((float)$p->total,2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>
@endsection
