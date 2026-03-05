@extends('layouts.app')

@section('title', 'Bitácora')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold">Bitácora 🧾</h1>
            <p class="text-slate-600">Últimos movimientos del sistema.</p>
        </div>

        <a href="{{ route('panel.index') }}"
           class="px-4 py-2 rounded-xl bg-white border border-slate-200 font-bold hover:bg-slate-50 transition">
            ← Volver
        </a>
    </div>

    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm overflow-x-auto">
        @if($logs->isEmpty())
            <p class="text-slate-600">No hay registros.</p>
        @else
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="text-left text-slate-600 border-b">
                        <th class="py-2 pr-4">Fecha</th>
                        <th class="py-2 pr-4">Usuario</th>
                        <th class="py-2 pr-4">Módulo</th>
                        <th class="py-2 pr-4">Acción</th>
                        <th class="py-2 pr-4">Entidad</th>
                        <th class="py-2 pr-4">Detalles</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $l)
                        <tr class="border-b last:border-b-0 hover:bg-slate-50">
                            <td class="py-3 pr-4 text-xs">{{ $l->created_at->format('d/m/Y H:i') }}</td>
                            <td class="py-3 pr-4 font-bold">{{ $l->user?->name ?? 'Sistema' }}</td>
                            <td class="py-3 pr-4">
                                @switch($l->module)
                                    @case('pos')
                                        <span class="px-2 py-1 rounded-full bg-emerald-100 text-emerald-700 text-xs font-bold">POS</span>
                                        @break
                                    @case('products')
                                        <span class="px-2 py-1 rounded-full bg-blue-100 text-blue-700 text-xs font-bold">Productos</span>
                                        @break
                                    @case('inventory')
                                        <span class="px-2 py-1 rounded-full bg-purple-100 text-purple-700 text-xs font-bold">Inventario</span>
                                        @break
                                    @case('users')
                                        <span class="px-2 py-1 rounded-full bg-amber-100 text-amber-700 text-xs font-bold">Usuarios</span>
                                        @break
                                    @case('caja')
                                        <span class="px-2 py-1 rounded-full bg-rose-100 text-rose-700 text-xs font-bold">Caja</span>
                                        @break
                                    @default
                                        <span class="px-2 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-bold">{{ $l->module }}</span>
                                @endswitch
                            </td>
                            <td class="py-3 pr-4 font-bold">
                                @switch($l->action)
                                    @case('sale.created')
                                        <span class="text-emerald-600">Venta realizada</span>
                                        @break
                                    @case('product.created')
                                        <span class="text-blue-600">Producto creado</span>
                                        @break
                                    @case('product.updated')
                                        <span class="text-blue-600">Producto editado</span>
                                        @break
                                    @case('inventory.entry')
                                        <span class="text-purple-600">Entrada mercancía</span>
                                        @break
                                    @case('seller.created')
                                        <span class="text-amber-600">Vendedor creado</span>
                                        @break
                                    @case('seller.toggled')
                                        <span class="text-amber-600">Vendedor activado/desactivado</span>
                                        @break
                                    @case('cash.opened')
                                        <span class="text-rose-600">Caja abierta</span>
                                        @break
                                    @case('cash.closed')
                                        <span class="text-rose-600">Caja cerrada</span>
                                        @break
                                    @case('cash.amount_registered')
                                        <span class="text-rose-600">Dinero real registrado</span>
                                        @break
                                    @default
                                        {{ $l->action }}
                                @endswitch
                            </td>
                            <td class="py-3 pr-4">
                                @if($l->entity_type)
                                    <div class="text-slate-700">
                                        @php
                                            $entityName = $l->meta['_entity_name'] ?? null;
                                            if (!$entityName && $l->entity_type === 'config') {
                                                $entityName = 'Configuración general';
                                            } elseif (!$entityName) {
                                                $entityName = $l->entity_type;
                                            }
                                        @endphp
                                        <span class="font-medium">{{ $entityName }}</span>
                                        @if($l->entity_id)
                                            <span class="text-slate-400 text-xs">#{{ $l->entity_id }}</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="py-3 pr-4 text-xs text-slate-700 max-w-sm">
                                @php
                                    $detalles = collect($l->meta ?? [])->except('_entity_name');
                                @endphp
                                @if($detalles->isNotEmpty())
                                    <div class="space-y-0.5">
                                        @foreach($detalles as $key => $value)
                                            <div class="flex items-start gap-1">
                                                <span class="font-semibold text-slate-500 capitalize">{{ $key }}:</span>
                                                <span class="{{ is_string($value) && str_contains($value, '→') ? 'text-blue-700 font-medium' : '' }}">
                                                    @if(is_array($value))
                                                        {{ implode(', ', $value) }}
                                                    @elseif(is_bool($value))
                                                        {{ $value ? 'sí' : 'no' }}
                                                    @else
                                                        {{ $value }}
                                                    @endif
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="mt-4">
                {{ $logs->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
