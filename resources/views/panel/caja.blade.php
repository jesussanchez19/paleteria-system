@extends('layouts.app')

@section('title', 'Caja')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold">Caja 💵</h1>
            <p class="text-slate-600">Monitoreo de apertura/cierre, diferencias y cortes.</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('panel.index') }}"
               class="px-4 py-2 rounded-xl bg-white border border-slate-200 font-bold hover:bg-slate-50 transition">
                ← Volver
            </a>
        </div>
    </div>

    {{-- Estadísticas globales --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
            <p class="text-xs text-slate-600">Total turnos</p>
            <p class="text-xl font-extrabold">{{ $stats['total_turnos'] }}</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
            <p class="text-xs text-slate-600">Turnos hoy</p>
            <p class="text-xl font-extrabold">{{ $stats['turnos_hoy'] }}</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
            <p class="text-xs text-slate-600">Diferencia total</p>
            <p class="text-xl font-extrabold {{ $stats['diferencia_total'] < 0 ? 'text-rose-600' : ($stats['diferencia_total'] > 0 ? 'text-emerald-600' : '') }}">
                ${{ number_format($stats['diferencia_total'], 2) }}
            </p>
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
            <p class="text-xs text-slate-600">Con faltante</p>
            <p class="text-xl font-extrabold text-rose-600">{{ $stats['turnos_con_faltante'] }}</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
            <p class="text-xs text-slate-600">Con sobrante</p>
            <p class="text-xl font-extrabold text-emerald-600">{{ $stats['turnos_con_sobrante'] }}</p>
        </div>
    </div>

    {{-- Filtro por fecha --}}
    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
        <form method="GET" action="{{ route('panel.caja.index') }}" class="flex flex-col sm:flex-row gap-3 sm:items-end">
            <div>
                <label class="text-sm font-bold text-slate-700">Fecha</label>
                <input type="date" name="date" value="{{ $date }}"
                       class="mt-1 rounded-xl border border-slate-200 px-3 py-2">
            </div>

            <button type="submit"
                    class="px-5 py-3 rounded-2xl bg-slate-900 text-white font-extrabold hover:bg-slate-800 transition">
                Ver
            </button>

            <div class="sm:ml-auto text-sm text-slate-600">
                Ventas del día: <b>${{ number_format($expected, 2) }}</b>
            </div>
        </form>
    </div>

    {{-- Caja del día --}}
    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-extrabold">Caja del día - {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}</h2>
            @if($cash)
                @if($cash->closed_at)
                    <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-700 font-bold text-sm">CERRADA</span>
                @else
                    <span class="px-3 py-1 rounded-full bg-amber-100 text-amber-700 font-bold text-sm">ABIERTA</span>
                @endif
            @endif
        </div>

        @if(!$cash)
            <p class="text-slate-600">No hay registro de caja para esta fecha.</p>
        @else
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-4">
                <div class="bg-slate-50 rounded-xl p-3">
                    <p class="text-xs text-slate-600">Apertura</p>
                    <p class="text-lg font-bold">${{ number_format((float)$cash->opening_amount, 2) }}</p>
                    <p class="text-xs text-slate-500">{{ optional($cash->opened_at)->format('H:i') }}</p>
                </div>
                <div class="bg-slate-50 rounded-xl p-3">
                    <p class="text-xs text-slate-600">Ventas turno</p>
                    <p class="text-lg font-bold">${{ number_format($salesDuringShift, 2) }}</p>
                </div>
                <div class="bg-blue-50 rounded-xl p-3">
                    <p class="text-xs text-blue-600">Esperado</p>
                    <p class="text-lg font-extrabold text-blue-700">${{ number_format($expectedInCash, 2) }}</p>
                </div>
                @if($cash->closed_at && $cash->closing_amount !== null)
                <div class="bg-slate-50 rounded-xl p-3">
                    <p class="text-xs text-slate-600">Cierre real</p>
                    <p class="text-lg font-bold">${{ number_format((float)$cash->closing_amount, 2) }}</p>
                    <p class="text-xs text-slate-500">{{ optional($cash->closed_at)->format('H:i') }}</p>
                </div>
                <div class="{{ $cash->difference >= 0 ? 'bg-emerald-50' : 'bg-rose-50' }} rounded-xl p-3">
                    <p class="text-xs {{ $cash->difference >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">Diferencia</p>
                    <p class="text-lg font-extrabold {{ $cash->difference >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                        {{ $cash->difference >= 0 ? '+' : '' }}${{ number_format((float)$cash->difference, 2) }}
                    </p>
                </div>
                @elseif($cash->closed_at && $cash->closing_amount === null)
                <div class="bg-amber-50 rounded-xl p-3">
                    <p class="text-xs text-amber-600">Cierre real</p>
                    <p class="text-lg font-bold text-amber-700">Pendiente</p>
                    <p class="text-xs text-slate-500">{{ optional($cash->closed_at)->format('H:i') }}</p>
                </div>
                @endif
            </div>

            <div class="text-sm text-slate-600 mb-4">
                Usuario: <b>{{ $cash->user?->name ?? 'Sistema' }}</b>
            </div>

            {{-- Estado de la caja --}}
            @if(!$cash->closed_at)
                {{-- Caja abierta --}}
                <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
                    <div class="flex items-center gap-3">
                        <span class="text-2xl">⏰</span>
                        <div>
                            <p class="text-amber-800 font-bold">Caja abierta</p>
                            <p class="text-sm text-slate-600">El cierre automático es a las 5:00pm</p>
                        </div>
                    </div>
                </div>
            @elseif($cash->closing_amount === null)
                {{-- Caja cerrada pero pendiente de ingresar dinero real --}}
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                    <p class="text-blue-800 font-bold mb-3">💵 Registrar dinero real</p>
                    <p class="text-sm text-slate-600 mb-3">La caja se cerró automáticamente. Ingresa el dinero real contado.</p>
                    <form method="POST" action="{{ route('caja.registrar.dinero') }}" 
                          class="flex flex-col sm:flex-row items-start sm:items-end gap-3">
                        @csrf
                        <input type="hidden" name="cash_register_id" value="{{ $cash->id }}">
                        <div class="flex-1 w-full sm:w-auto">
                            <label class="text-sm text-slate-700">Dinero real en caja:</label>
                            <input type="number" step="0.01" name="closing_amount" 
                                   placeholder="Ingresa el monto" required
                                   class="mt-1 w-full px-4 py-3 rounded-xl border border-slate-200 text-lg font-bold">
                        </div>
                        <button type="submit" 
                                class="px-6 py-3 rounded-xl bg-blue-500 text-white font-bold hover:bg-blue-600 transition">
                            Registrar
                        </button>
                    </form>
                    <p class="text-xs text-slate-500 mt-2">Esperado: ${{ number_format($expectedInCash, 2) }}</p>
                </div>
            @else
                {{-- Caja cerrada con dinero registrado --}}
                <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-emerald-700 font-bold">✓ Caja cerrada correctamente</p>
                            <p class="text-sm text-slate-600">Dinero reportado: <b>${{ number_format((float)$cash->closing_amount, 2) }}</b></p>
                        </div>
                        <div class="text-right">
                            @if($cash->difference == 0)
                                <span class="text-emerald-600 font-bold text-lg">Cuadra perfecto</span>
                            @elseif($cash->difference > 0)
                                <span class="text-emerald-600 font-bold text-lg">↑ Sobrante</span>
                            @else
                                <span class="text-rose-600 font-bold text-lg">↓ Faltante</span>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        @endif
    </div>

    {{-- Historial --}}
    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
        <h2 class="text-lg font-extrabold mb-4">Historial de turnos</h2>

        @if($registers->isEmpty())
            <p class="text-slate-600">Sin historial.</p>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="text-left text-slate-600 border-b">
                            <th class="py-2 pr-4">ID</th>
                            <th class="py-2 pr-4">Fecha</th>
                            <th class="py-2 pr-4">Usuario</th>
                            <th class="py-2 pr-4">Apertura</th>
                            <th class="py-2 pr-4">Esperado</th>
                            <th class="py-2 pr-4">Cierre</th>
                            <th class="py-2 pr-4">Diferencia</th>
                            <th class="py-2 pr-4">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($registers as $c)
                            <tr class="border-b last:border-b-0 hover:bg-slate-50">
                                <td class="py-3 pr-4 font-bold">#{{ $c->id }}</td>
                                <td class="py-3 pr-4">{{ optional($c->opened_at)->format('d/m/Y') }}</td>
                                <td class="py-3 pr-4">{{ $c->user?->name ?? 'Sistema' }}</td>
                                <td class="py-3 pr-4">${{ number_format((float)$c->opening_amount, 2) }}</td>
                                <td class="py-3 pr-4">
                                    {{ $c->expected_amount === null ? '—' : '$'.number_format((float)$c->expected_amount, 2) }}
                                </td>
                                <td class="py-3 pr-4">
                                    {{ $c->closing_amount === null ? '—' : '$'.number_format((float)$c->closing_amount, 2) }}
                                </td>
                                <td class="py-3 pr-4">
                                    @if($c->difference === null)
                                        —
                                    @elseif($c->difference >= 0)
                                        <span class="text-emerald-600 font-bold">${{ number_format((float)$c->difference, 2) }}</span>
                                    @else
                                        <span class="text-rose-600 font-bold">${{ number_format((float)$c->difference, 2) }}</span>
                                    @endif
                                </td>
                                <td class="py-3 pr-4">
                                    @if($c->closed_at && $c->closing_amount !== null)
                                        <span class="px-2 py-1 rounded-full bg-emerald-100 text-emerald-700 font-bold text-xs">CERRADA</span>
                                    @elseif($c->closed_at && $c->closing_amount === null)
                                        <span class="px-2 py-1 rounded-full bg-blue-100 text-blue-700 font-bold text-xs">PENDIENTE</span>
                                    @else
                                        <span class="px-2 py-1 rounded-full bg-amber-100 text-amber-700 font-bold text-xs">ABIERTA</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $registers->appends(['date' => $date])->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
