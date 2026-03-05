@extends('layouts.app')

@section('title', 'Reporte por vendedores')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold">Reporte por vendedores 👥</h1>
            <p class="text-slate-600">Ventas por usuario ({{ $date === now()->toDateString() ? 'Hoy' : \Carbon\Carbon::parse($date)->format('d/m/Y') }}).</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('reportes.index') }}"
               class="px-4 py-2 rounded-xl bg-white border border-slate-200 font-bold hover:bg-slate-50 transition">
                ← Volver a reportes
            </a>
        </div>
    </div>

    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
        <form method="GET" action="{{ route('panel.reportes.vendedores') }}" class="flex flex-col sm:flex-row gap-3 sm:items-end">
            <div>
                <label class="text-sm font-bold text-slate-700">Fecha</label>
                <input type="date" name="date" value="{{ $date }}"
                       class="mt-1 rounded-xl border border-slate-200 px-3 py-2">
            </div>

            <button type="submit"
                    class="px-5 py-3 rounded-2xl bg-slate-900 text-white font-extrabold hover:bg-slate-800 transition">
                Filtrar
            </button>

            <div class="sm:ml-auto text-sm text-slate-600">
                Total día: <b>${{ number_format($grandTotal,2) }}</b> • Ventas: <b>{{ $grandCount }}</b>
            </div>
        </form>
    </div>

    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
        <h2 class="text-lg font-extrabold">Resultados</h2>

        @if($rows->isEmpty())
            <p class="text-slate-600 mt-3">{{ $date === now()->toDateString() ? 'Hoy' : 'El ' . \Carbon\Carbon::parse($date)->format('d/m/Y') }} no hay ventas registradas.</p>
        @else
            <div class="mt-4 overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="text-left text-slate-600 border-b">
                            <th class="py-2 pr-4">Vendedor</th>
                            <th class="py-2 pr-4">Email</th>
                            <th class="py-2 pr-4 text-right"># Ventas</th>
                            <th class="py-2 pr-4 text-right">Total</th>
                            <th class="py-2 pr-4 text-right">Ticket promedio</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rows as $r)
                            <tr class="border-b last:border-b-0">
                                <td class="py-3 pr-4 font-bold">{{ $r->name }}</td>
                                <td class="py-3 pr-4">{{ $r->email }}</td>
                                <td class="py-3 pr-4 text-right">{{ (int)$r->num_sales }}</td>
                                <td class="py-3 pr-4 text-right font-extrabold">${{ number_format((float)$r->total_sales,2) }}</td>
                                <td class="py-3 pr-4 text-right">${{ number_format((float)$r->avg_ticket,2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>
@endsection
