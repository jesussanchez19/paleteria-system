@extends('layouts.app')

@section('title', 'Ventas')

@section('page_title', 'Ventas 🧾')
@section('page_subtitle', 'Historial de ventas (demo). Después lo conectamos con la BD.')

@section('content')
    <div class="rounded-3xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-5 flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-between">
            <div class="text-sm text-slate-600">
                Hoy: <span class="font-semibold">$0.00</span> (demo)
            </div>

            <div class="flex gap-2">
                <button class="px-4 py-2 rounded-xl bg-sky-600 text-white font-semibold hover:bg-sky-700 transition shadow-sm">
                    + Nueva venta
                </button>
                <button class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 font-semibold hover:bg-slate-200 transition">
                    Exportar PDF
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="text-left px-5 py-3 font-semibold">ID</th>
                        <th class="text-left px-5 py-3 font-semibold">Total</th>
                        <th class="text-left px-5 py-3 font-semibold">Fecha</th>
                        <th class="text-right px-5 py-3 font-semibold">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @php
                        $sales = [
                            ['id' => 1, 'total' => '$120.00', 'date' => '2026-01-26 10:15'],
                            ['id' => 2, 'total' => '$80.00',  'date' => '2026-01-26 12:40'],
                            ['id' => 3, 'total' => '$210.00', 'date' => '2026-01-26 15:05'],
                        ];
                    @endphp

                    @foreach($sales as $s)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-5 py-3 font-semibold">#{{ $s['id'] }}</td>
                            <td class="px-5 py-3">{{ $s['total'] }}</td>
                            <td class="px-5 py-3 text-slate-600">{{ $s['date'] }}</td>
                            <td class="px-5 py-3 text-right">
                                <button class="px-3 py-2 rounded-xl bg-slate-900 text-white font-semibold hover:bg-slate-800 transition">
                                    Ver
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="p-5 text-xs text-slate-500">
            * Datos de ejemplo. Luego conectamos con PostgreSQL.
        </div>
    </div>
@endsection
