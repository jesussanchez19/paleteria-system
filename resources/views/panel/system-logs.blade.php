@extends('layouts.app')

@section('title', 'Logs del sistema')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold">Logs del sistema 📄</h1>
            <p class="text-slate-600">Últimas líneas de storage/logs/laravel.log para diagnóstico técnico.</p>
        </div>

        <div class="flex gap-2">
            <a href="{{ route('panel.config.critica') }}"
               class="px-4 py-2 rounded-xl bg-white border border-slate-200 font-bold hover:bg-slate-50 transition">
                ← Volver
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
            <p class="text-xs text-slate-500">Archivo</p>
            <p class="mt-1 text-sm font-bold text-slate-800">laravel.log</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
            <p class="text-xs text-slate-500">Estado</p>
            <p class="mt-1 text-sm font-bold {{ $logInfo['exists'] ? 'text-emerald-700' : 'text-rose-700' }}">
                {{ $logInfo['exists'] ? 'Disponible' : 'No encontrado' }}
            </p>
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
            <p class="text-xs text-slate-500">Tamaño</p>
            <p class="mt-1 text-sm font-bold text-slate-800">{{ $logInfo['size'] }}</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
            <p class="text-xs text-slate-500">Última actualización</p>
            <p class="mt-1 text-sm font-bold text-slate-800">{{ $logInfo['updated_at'] ?? 'Sin datos' }}</p>
        </div>
    </div>

    <div class="bg-blue-50 border border-blue-200 rounded-2xl p-4 text-sm text-blue-900">
        <p class="font-semibold">Qué revisar aquí:</p>
        <p class="mt-1">Errores de Cloudinary, fallos de API, excepciones PHP, problemas de permisos y errores internos de Laravel. Para movimientos operativos del negocio, usa la bitácora de auditoría del sistema.</p>
    </div>

    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h2 class="text-lg font-extrabold text-slate-700">Últimas {{ $logInfo['line_count'] }} líneas</h2>
                <p class="text-xs text-slate-500">Ruta: {{ $logInfo['path'] }}</p>
            </div>

            <div class="flex gap-2">
                <a href="{{ route('panel.config.critica.logs', ['lines' => 100]) }}" class="px-3 py-2 rounded-xl border border-slate-200 text-sm font-bold hover:bg-slate-50 transition">100</a>
                <a href="{{ route('panel.config.critica.logs', ['lines' => 200]) }}" class="px-3 py-2 rounded-xl border border-slate-200 text-sm font-bold hover:bg-slate-50 transition">200</a>
                <a href="{{ route('panel.config.critica.logs', ['lines' => 500]) }}" class="px-3 py-2 rounded-xl border border-slate-200 text-sm font-bold hover:bg-slate-50 transition">500</a>
            </div>
        </div>

        <div class="mt-4 rounded-xl bg-slate-950 text-slate-100 p-4 overflow-x-auto">
            @if(!$logInfo['exists'])
                <p class="text-sm text-rose-300">No se encontró el archivo de logs en storage/logs/laravel.log.</p>
            @elseif(empty($logLines))
                <p class="text-sm text-slate-300">El archivo existe pero no contiene líneas para mostrar.</p>
            @else
                <pre class="text-xs leading-6 whitespace-pre-wrap break-all">@foreach($logLines as $line){{ $line }}
@endforeach</pre>
            @endif
        </div>
    </div>

</div>
@endsection