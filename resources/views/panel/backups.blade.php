@extends('layouts.app')

@section('title', 'Gestión de Respaldos')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-800">💾 Gestión de Respaldos</h1>
            <p class="text-sm text-slate-500 mt-1">Crea, descarga y administra los backups de tu base de datos</p>
        </div>
        <a href="{{ route('panel.config.critica') }}" 
           class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 text-slate-700 rounded-xl font-semibold hover:bg-slate-200 transition">
            ← Volver a configuración
        </a>
    </div>

    {{-- Mensajes de estado --}}
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl flex items-start gap-3">
            <span class="text-2xl">✅</span>
            <div>
                <p class="font-semibold">Operación exitosa</p>
                <p class="text-sm">{{ session('success') }}</p>
            </div>
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 bg-rose-50 border border-rose-200 text-rose-700 rounded-xl flex items-start gap-3">
            <span class="text-2xl">❌</span>
            <div>
                <p class="font-semibold">Error</p>
                <p class="text-sm">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    {{-- Estadísticas rápidas --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center text-2xl">📦</div>
                <div>
                    <p class="text-2xl font-extrabold text-blue-600">{{ count($backups) }}</p>
                    <p class="text-sm text-slate-500">Backups disponibles</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center text-2xl">💿</div>
                <div>
                    <p class="text-2xl font-extrabold text-emerald-600">{{ $totalSize }}</p>
                    <p class="text-sm text-slate-500">Espacio usado</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl {{ $autoBackupEnabled ? 'bg-green-100' : 'bg-amber-100' }} flex items-center justify-center text-2xl">
                    {{ $autoBackupEnabled ? '🔄' : '⏸️' }}
                </div>
                <div>
                    <p class="text-lg font-extrabold {{ $autoBackupEnabled ? 'text-green-600' : 'text-amber-600' }}">
                        {{ $autoBackupEnabled ? 'Activado' : 'Desactivado' }}
                    </p>
                    <p class="text-sm text-slate-500">Backup automático</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Acciones principales --}}
    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
        <h2 class="text-lg font-extrabold text-slate-700 mb-4">⚡ Acciones rápidas</h2>
        
        <div class="flex flex-wrap gap-3">
            {{-- Crear backup manual --}}
            <form action="{{ route('panel.backups.create') }}" method="POST" class="inline">
                @csrf
                <button type="submit" 
                        class="inline-flex items-center gap-2 px-5 py-3 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700 transition shadow-md hover:shadow-lg"
                        onclick="this.disabled=true; this.innerHTML='⏳ Creando...'; this.form.submit();">
                    ➕ Crear backup ahora
                </button>
            </form>

            {{-- Descargar último backup --}}
            @if(count($backups) > 0)
                <a href="{{ route('panel.backups.download', $backups[0]['filename']) }}" 
                   class="inline-flex items-center gap-2 px-5 py-3 bg-emerald-600 text-white rounded-xl font-bold hover:bg-emerald-700 transition shadow-md hover:shadow-lg">
                    ⬇️ Descargar último
                </a>
            @endif
        </div>

        <div class="mt-4 p-3 bg-amber-50 border border-amber-200 rounded-xl">
            <p class="text-sm text-amber-800">
                <strong>⚠️ Importante:</strong> En Railway, los backups se eliminan con cada nuevo deploy. 
                <strong>Descarga inmediatamente</strong> cualquier backup que necesites conservar.
            </p>
        </div>
    </div>

    {{-- Configuración de backup automático --}}
    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
        <h2 class="text-lg font-extrabold text-slate-700 mb-4">⚙️ Configuración de backups automáticos</h2>
        
        <form action="{{ route('panel.backups.config') }}" method="POST" class="space-y-4">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1">🔄 Backup automático diario</label>
                    <select name="auto_backup_enabled" class="w-full rounded-xl border border-slate-200 px-4 py-3">
                        <option value="0" {{ !$autoBackupEnabled ? 'selected' : '' }}>❌ Desactivado</option>
                        <option value="1" {{ $autoBackupEnabled ? 'selected' : '' }}>✅ Activado</option>
                    </select>
                    <p class="text-xs text-slate-500 mt-1">Se ejecutará automáticamente cada día</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1">📅 Días de retención</label>
                    <input type="number" name="backup_retention_days" value="{{ $retentionDays }}" 
                           min="1" max="365"
                           class="w-full rounded-xl border border-slate-200 px-4 py-3">
                    <p class="text-xs text-slate-500 mt-1">Eliminar backups más antiguos automáticamente</p>
                </div>
            </div>

            <button type="submit" 
                    class="px-5 py-2 bg-slate-800 text-white rounded-xl font-bold hover:bg-slate-900 transition">
                💾 Guardar configuración
            </button>
        </form>
    </div>

    {{-- Lista de backups --}}
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
            <h2 class="text-lg font-extrabold text-slate-700">📁 Backups disponibles</h2>
            <span class="text-sm text-slate-500">{{ count($backups) }} archivo(s)</span>
        </div>
        
        @if(count($backups) > 0)
            <div class="divide-y divide-slate-100">
                @foreach($backups as $index => $backup)
                    <div class="px-6 py-4 hover:bg-slate-50 transition {{ $index === 0 ? 'bg-blue-50/50' : '' }}">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="flex items-start gap-4">
                                <div class="w-10 h-10 rounded-lg {{ $index === 0 ? 'bg-blue-100' : 'bg-slate-100' }} flex items-center justify-center text-lg flex-shrink-0">
                                    {{ $index === 0 ? '🆕' : '📄' }}
                                </div>
                                <div>
                                    <p class="font-semibold text-slate-800">{{ $backup['filename'] }}</p>
                                    <div class="flex flex-wrap items-center gap-2 mt-1">
                                        <span class="inline-flex items-center gap-1 text-xs text-slate-500">
                                            💿 {{ $backup['size'] }}
                                        </span>
                                        <span class="text-slate-300">•</span>
                                        <span class="inline-flex items-center gap-1 text-xs text-slate-500">
                                            📅 {{ $backup['date'] }}
                                        </span>
                                        <span class="text-slate-300">•</span>
                                        <span class="inline-flex items-center gap-1 text-xs text-slate-500">
                                            ⏰ {{ $backup['age'] }}
                                        </span>
                                        @if($index === 0)
                                            <span class="inline-flex items-center px-2 py-0.5 text-xs font-semibold bg-blue-100 text-blue-700 rounded-full">
                                                Más reciente
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex items-center gap-2 sm:flex-shrink-0">
                                <a href="{{ route('panel.backups.download', $backup['filename']) }}" 
                                   class="inline-flex items-center gap-1 px-4 py-2 text-sm font-bold text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 transition">
                                    ⬇️ Descargar
                                </a>
                                <form action="{{ route('panel.backups.delete', $backup['filename']) }}" 
                                      method="POST" 
                                      onsubmit="return confirm('¿Eliminar este backup permanentemente?\n\n{{ $backup['filename'] }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="inline-flex items-center gap-1 px-4 py-2 text-sm font-bold text-rose-600 bg-rose-50 rounded-lg hover:bg-rose-100 transition">
                                        🗑️ Eliminar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="px-6 py-12 text-center">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-slate-100 flex items-center justify-center text-3xl">
                    📭
                </div>
                <h3 class="text-lg font-semibold text-slate-700">No hay backups disponibles</h3>
                <p class="text-sm text-slate-500 mt-1">Crea tu primer backup usando el botón de arriba</p>
            </div>
        @endif
    </div>

    {{-- Información adicional --}}
    <div class="bg-slate-50 border border-slate-200 rounded-2xl p-6">
        <h3 class="font-bold text-slate-700 mb-3">ℹ️ Información sobre respaldos</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-slate-600">
            <div>
                <p class="font-semibold mb-1">¿Qué incluye el backup?</p>
                <ul class="list-disc list-inside space-y-1 text-slate-500">
                    <li>Usuarios y configuración</li>
                    <li>Productos e inventario</li>
                    <li>Ventas y detalles</li>
                    <li>Registros de caja</li>
                    <li>Bitácora de auditoría</li>
                </ul>
            </div>
            <div>
                <p class="font-semibold mb-1">¿Cómo restaurar un backup?</p>
                <ul class="list-disc list-inside space-y-1 text-slate-500">
                    <li>Descarga el archivo .sql</li>
                    <li>Accede a tu servidor PostgreSQL</li>
                    <li>Ejecuta: <code class="bg-slate-200 px-1 rounded">psql -f archivo.sql</code></li>
                    <li>O usa pgAdmin para importar</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
