@extends('layouts.app')
@section('title', 'Reporte diario público')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div>
        <h1 class="text-2xl sm:text-3xl font-extrabold">Reporte diario (público) 📌</h1>
        <p class="text-slate-600">Fecha: <b>{{ $date }}</b></p>
        <p class="text-xs text-slate-500">Este enlace se usa para el QR del reporte.</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <p class="text-sm text-slate-600">Ventas del día</p>
            <p class="text-3xl font-extrabold">{{ $salesCount }}</p>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <p class="text-sm text-slate-600">Total del día</p>
            <p class="text-3xl font-extrabold">${{ number_format($total, 2) }}</p>
        </div>
    </div>

    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
        <h2 class="text-lg font-extrabold">Resumen por producto</h2>

        @if($byProduct->isEmpty())
            <p class="text-slate-600 mt-2">Hoy no hay ventas registradas.</p>
        @else
            <div class="mt-3 overflow-x-auto">
                <table class="min-w-full text-sm">
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

</div>
@endsection
