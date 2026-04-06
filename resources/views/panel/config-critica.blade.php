@extends('layouts.app')

@section('title', 'Configuración crítica')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="flex items-end justify-between bg-gradient-to-r from-slate-800 to-slate-700 rounded-2xl p-5 shadow-lg">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-white">Configuración crítica 🔐</h1>
            <p class="text-white/70">Solo administradores. Ajustes sensibles del sistema.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 font-semibold">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
        <div class="flex items-center gap-2">
            <span class="text-xl">⚠️</span>
            <p class="text-sm text-amber-800 font-semibold">Precaución: Estos ajustes afectan el funcionamiento crítico del sistema.</p>
        </div>
    </div>

    {{-- Computadora de trabajo --}}
    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl p-5 shadow-lg text-white">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-extrabold mb-1">💻 Computadora de trabajo</h2>
                @if(request()->cookie('work_computer') === 'true')
                    <p class="text-blue-100 text-sm">Esta PC está marcada como computadora de trabajo. Al entrar a la URL principal irá directo al login.</p>
                @else
                    <p class="text-blue-100 text-sm">Esta PC NO está marcada como de trabajo. La URL principal muestra el catálogo público.</p>
                @endif
            </div>
            <div>
                @if(request()->cookie('work_computer') === 'true')
                    <a href="{{ route('desmarcar.pc.trabajo') }}" 
                       class="px-4 py-2 bg-white text-blue-600 rounded-xl font-bold hover:bg-blue-50 transition inline-flex items-center gap-2">
                        ❌ Desmarcar PC
                    </a>
                @else
                    <a href="{{ route('marcar.pc.trabajo') }}" 
                       class="px-4 py-2 bg-white text-blue-600 rounded-xl font-bold hover:bg-blue-50 transition inline-flex items-center gap-2">
                        ✅ Marcar como PC de trabajo
                    </a>
                @endif
            </div>
        </div>
    </div>

    {{-- Dashboard del Sistema --}}
    <div class="bg-gradient-to-r from-slate-800 to-slate-700 rounded-2xl p-5 shadow-lg text-white">
        <h2 class="text-lg font-extrabold mb-4">📊 Estado del Sistema</h2>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="bg-white/10 rounded-xl p-3 text-center">
                <div class="text-2xl font-bold">{{ $stats['total_usuarios'] }}</div>
                <div class="text-xs text-slate-300">Usuarios</div>
            </div>
            <div class="bg-white/10 rounded-xl p-3 text-center">
                <div class="text-2xl font-bold">{{ $stats['vendedores_activos'] }}</div>
                <div class="text-xs text-slate-300">Vendedores activos</div>
            </div>
            <div class="bg-white/10 rounded-xl p-3 text-center">
                <div class="text-2xl font-bold">{{ $stats['productos_activos'] }}</div>
                <div class="text-xs text-slate-300">Productos activos</div>
            </div>
            <div class="bg-white/10 rounded-xl p-3 text-center {{ $stats['productos_bajo_stock'] > 0 ? 'bg-amber-500/30' : '' }}">
                <div class="text-2xl font-bold">{{ $stats['productos_bajo_stock'] }}</div>
                <div class="text-xs text-slate-300">Stock bajo</div>
            </div>
        </div>
        
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-4">
            <div class="bg-white/10 rounded-xl p-3 text-center">
                <div class="text-2xl font-bold">{{ $stats['ventas_hoy'] }}</div>
                <div class="text-xs text-slate-300">Ventas hoy</div>
            </div>
            <div class="bg-white/10 rounded-xl p-3 text-center">
                <div class="text-2xl font-bold">${{ number_format($stats['ingresos_hoy'], 0) }}</div>
                <div class="text-xs text-slate-300">Ingresos hoy</div>
            </div>
            <div class="bg-white/10 rounded-xl p-3 text-center">
                <div class="text-2xl font-bold">{{ $stats['ventas_mes'] }}</div>
                <div class="text-xs text-slate-300">Ventas mes</div>
            </div>
            <div class="bg-white/10 rounded-xl p-3 text-center">
                <div class="text-2xl font-bold">${{ number_format($stats['ingresos_mes'], 0) }}</div>
                <div class="text-xs text-slate-300">Ingresos mes</div>
            </div>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-4 text-xs">
            <div class="bg-white/5 rounded-lg p-2">
                <span class="text-slate-400">PHP:</span> <span class="font-mono">{{ $stats['php_version'] }}</span>
            </div>
            <div class="bg-white/5 rounded-lg p-2">
                <span class="text-slate-400">Laravel:</span> <span class="font-mono">{{ $stats['laravel_version'] }}</span>
            </div>
            <div class="bg-white/5 rounded-lg p-2">
                <span class="text-slate-400">BD:</span> <span class="font-mono">{{ $stats['db_size'] }}</span>
            </div>
            <div class="bg-white/5 rounded-lg p-2">
                <span class="text-slate-400">Logs:</span> <span>{{ number_format($stats['logs_auditoria']) }}</span>
            </div>
        </div>
    </div>

    {{-- Herramientas de Mantenimiento --}}
    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
        <h2 class="text-lg font-extrabold mb-4 text-slate-600">🔧 Herramientas de Mantenimiento</h2>

        <div class="mb-5">
            <h3 class="font-bold text-sm mb-3 text-slate-700">Conexiones e integraciones detectadas:</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($systemConnections as $connection)
                    <div class="rounded-xl border {{ $connection['configured'] ? 'border-emerald-200 bg-emerald-50' : 'border-amber-200 bg-amber-50' }} p-3">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <p class="text-sm font-bold text-slate-800">{{ $connection['label'] }}</p>
                                <p class="text-xs text-slate-500">{{ $connection['type'] }}</p>
                            </div>
                            <span class="text-xs font-bold {{ $connection['configured'] ? 'text-emerald-600' : 'text-amber-600' }}">
                                {{ $connection['configured'] ? 'Configurada' : 'Pendiente' }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-600 mt-2">{{ $connection['details'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
        
        @if(session('success_tools'))
            <div class="mb-4 p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 font-semibold">
                {{ session('success_tools') }}
            </div>
        @endif

        @if(session('error_tools'))
            <div class="mb-4 p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 font-semibold">
                {{ session('error_tools') }}
            </div>
        @endif

        @if(session('connection_results'))
            <div class="mb-4 p-4 rounded-xl bg-slate-50 border border-slate-200">
                <h3 class="font-bold text-sm mb-2">Resultados de conexión:</h3>
                <div class="space-y-1 text-sm">
                    @foreach(session('connection_results') as $service => $result)
                        <div class="flex items-center gap-2">
                            @if($result['status'] === 'ok')
                                <span class="text-emerald-500">✅</span>
                            @elseif($result['status'] === 'warning')
                                <span class="text-amber-500">⚠️</span>
                            @else
                                <span class="text-rose-500">❌</span>
                            @endif
                            <span class="font-medium">{{ $result['label'] ?? str_replace('_', ' ', $service) }}:</span>
                            <span class="text-slate-600">{{ $result['message'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            {{-- Limpiar caché --}}
            <form method="POST" action="{{ route('panel.config.critica.clear-cache') }}" class="block">
                @csrf
                <button type="submit" class="w-full p-4 rounded-xl border-2 border-dashed border-slate-300 hover:border-blue-400 hover:bg-blue-50 transition text-center">
                    <span class="text-2xl block mb-1">🧹</span>
                    <span class="font-bold text-slate-600">Limpiar Caché</span>
                    <span class="text-xs text-slate-400 block">Config, vistas, rutas</span>
                </button>
            </form>

            {{-- Probar conexiones --}}
            <form method="POST" action="{{ route('panel.config.critica.test-connections') }}" class="block">
                @csrf
                <button type="submit" class="w-full p-4 rounded-xl border-2 border-dashed border-slate-300 hover:border-emerald-400 hover:bg-emerald-50 transition text-center">
                    <span class="text-2xl block mb-1">🔌</span>
                    <span class="font-bold text-slate-600">Probar Conexiones</span>
                    <span class="text-xs text-slate-400 block">BD, APIs, Cloudinary y storage</span>
                </button>
            </form>

            {{-- Limpiar logs antiguos --}}
            <form method="POST" action="{{ route('panel.config.critica.clean-logs') }}" 
                  onsubmit="return confirm('¿Eliminar registros de auditoría con más de 90 días?')" class="block">
                @csrf
                <input type="hidden" name="days" value="90">
                <button type="submit" class="w-full p-4 rounded-xl border-2 border-dashed border-slate-300 hover:border-amber-400 hover:bg-amber-50 transition text-center">
                    <span class="text-2xl block mb-1">🗑️</span>
                    <span class="font-bold text-slate-600">Limpiar Logs</span>
                    <span class="text-xs text-slate-400 block">Más de 90 días</span>
                </button>
            </form>
        </div>
    </div>

    {{-- Gestión del Gerente --}}
    @if($gerente)
    <div class="bg-white border border-indigo-200 rounded-2xl p-5 shadow-sm">
        <h2 class="text-lg font-extrabold mb-4 text-slate-600">👤 Gestión del Gerente</h2>
        
        @if(session('success_gerente'))
            <div class="mb-4 p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 font-semibold">
                {{ session('success_gerente') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 font-semibold">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('panel.config.critica.gerente') }}">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-semibold text-slate-600">📝 Nombre</label>
                    <input type="text" name="gerente_name" value="{{ old('gerente_name', $gerente->name) }}"
                           class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2" required>
                    @error('gerente_name') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="text-sm font-semibold text-slate-600">📧 Email</label>
                    <input type="email" name="gerente_email" value="{{ old('gerente_email', $gerente->email) }}"
                           class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2" required>
                    @error('gerente_email') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="text-sm font-semibold text-slate-600">🔑 Nueva contraseña</label>
                    <input type="password" name="gerente_password" 
                           class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2"
                           placeholder="Dejar vacío para no cambiar">
                    <p class="text-xs text-slate-500 mt-1">Mínimo 8 caracteres</p>
                    @error('gerente_password') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="text-sm font-semibold text-slate-600">🔑 Confirmar contraseña</label>
                    <input type="password" name="gerente_password_confirmation" 
                           class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2"
                           placeholder="Repetir contraseña">
                </div>
            </div>

            <div class="mt-4">
                <button type="submit"
                        class="px-5 py-2 rounded-xl bg-indigo-600 text-white font-bold hover:bg-indigo-700 transition">
                    Actualizar gerente
                </button>
            </div>
        </form>
    </div>
    @else
    <div class="bg-white border border-indigo-200 rounded-2xl p-5 shadow-sm">
        <h2 class="text-lg font-extrabold mb-4 text-slate-600">👤 Crear Gerente</h2>
        
        <div class="mb-4 p-3 rounded-xl bg-amber-50 border border-amber-200">
            <p class="text-sm text-amber-800">⚠️ No hay un gerente registrado. Crea uno para gestionar el sistema.</p>
        </div>

        @if(session('success_gerente'))
            <div class="mb-4 p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 font-semibold">
                {{ session('success_gerente') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 font-semibold">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('panel.config.critica.gerente.store') }}">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-semibold text-slate-600">📝 Nombre</label>
                    <input type="text" name="gerente_name" value="{{ old('gerente_name') }}"
                           class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2" required>
                    @error('gerente_name') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="text-sm font-semibold text-slate-600">📧 Email</label>
                    <input type="email" name="gerente_email" value="{{ old('gerente_email') }}"
                           class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2" required>
                    @error('gerente_email') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="text-sm font-semibold text-slate-600">🔑 Contraseña</label>
                    <input type="password" name="gerente_password" 
                           class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2" required>
                    <p class="text-xs text-slate-500 mt-1">Mínimo 8 caracteres</p>
                    @error('gerente_password') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="text-sm font-semibold text-slate-600">🔑 Confirmar contraseña</label>
                    <input type="password" name="gerente_password_confirmation" 
                           class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2" required>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit"
                        class="px-5 py-2 rounded-xl bg-emerald-600 text-white font-bold hover:bg-emerald-700 transition">
                    ➕ Crear gerente
                </button>
            </div>
        </form>
    </div>
    @endif

    <form method="POST" action="{{ route('panel.config.critica.update') }}" class="space-y-6">
        @csrf

        {{-- Estado del Sistema --}}
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <h2 class="text-lg font-extrabold mb-4 text-slate-600">⚡ Estado del Sistema</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-semibold text-slate-600">🛠️ Modo mantenimiento</label>
                    <select name="maintenance_mode" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
                        <option value="0" {{ $data['maintenance_mode'] == '0' ? 'selected' : '' }}>❌ Desactivado</option>
                        <option value="1" {{ $data['maintenance_mode'] == '1' ? 'selected' : '' }}>✅ Activado</option>
                    </select>
                    <p class="text-xs text-slate-500 mt-1">Solo admins pueden entrar</p>
                    @error('maintenance_mode') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="text-sm font-semibold text-slate-600">💰 Ventas habilitadas</label>
                    <select name="sales_enabled" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
                        <option value="1" {{ $data['sales_enabled'] == '1' ? 'selected' : '' }}>✅ Sí</option>
                        <option value="0" {{ $data['sales_enabled'] == '0' ? 'selected' : '' }}>❌ No</option>
                    </select>
                    <p class="text-xs text-slate-500 mt-1">Bloquear ventas en POS</p>
                    @error('sales_enabled') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Seguridad --}}
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <h2 class="text-lg font-extrabold mb-4 text-slate-600">🔒 Seguridad</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="text-sm font-semibold text-slate-600">🚫 Máx intentos login</label>
                    <input type="number" name="max_login_attempts" value="{{ $data['max_login_attempts'] }}" 
                           min="1" max="20"
                           class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
                    <p class="text-xs text-slate-500 mt-1">Antes de bloquear</p>
                    @error('max_login_attempts') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="text-sm font-semibold text-slate-600">⏱️ Timeout sesión (min)</label>
                    <input type="number" name="session_timeout" value="{{ $data['session_timeout'] }}" 
                           min="5" max="480"
                           class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
                    <p class="text-xs text-slate-500 mt-1">Inactividad máxima</p>
                    @error('session_timeout') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="text-sm font-semibold text-slate-600">🔑 Cambiar contraseña (días)</label>
                    <input type="number" name="force_password_change_days" value="{{ $data['force_password_change_days'] }}" 
                           min="0" max="365"
                           class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
                    <p class="text-xs text-slate-500 mt-1">0 = nunca forzar</p>
                    @error('force_password_change_days') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Límites de Ventas --}}
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <h2 class="text-lg font-extrabold mb-4 text-slate-600">💵 Límites de Ventas</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-semibold text-slate-600">💳 Monto máx sin autorización ($)</label>
                    <input type="number" name="max_sale_without_auth" value="{{ $data['max_sale_without_auth'] }}" 
                           min="0" step="100"
                           class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
                    <p class="text-xs text-slate-500 mt-1">Ventas mayores requieren supervisor</p>
                    @error('max_sale_without_auth') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="text-sm font-semibold text-slate-600">🏷️ Descuento máximo (%)</label>
                    <input type="number" name="max_discount_percent" value="{{ $data['max_discount_percent'] }}" 
                           min="0" max="100"
                           class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
                    <p class="text-xs text-slate-500 mt-1">Límite para vendedores</p>
                    @error('max_discount_percent') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Backup --}}
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <h2 class="text-lg font-extrabold mb-4 text-slate-600">💾 Respaldos</h2>
            
            {{-- Mensajes de estado --}}
            @if(session('success_backup'))
                <div class="mb-4 p-3 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl text-sm">
                    ✅ {{ session('success_backup') }}
                </div>
            @endif
            @if(session('error_backup'))
                <div class="mb-4 p-3 bg-rose-50 border border-rose-200 text-rose-700 rounded-xl text-sm">
                    ❌ {{ session('error_backup') }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="text-sm font-semibold text-slate-600">🔄 Backup automático</label>
                    <select name="auto_backup_enabled" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
                        <option value="0" {{ $data['auto_backup_enabled'] == '0' ? 'selected' : '' }}>❌ Desactivado</option>
                        <option value="1" {{ $data['auto_backup_enabled'] == '1' ? 'selected' : '' }}>✅ Activado</option>
                    </select>
                    <p class="text-xs text-slate-500 mt-1">Respaldo diario automático</p>
                    @error('auto_backup_enabled') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="text-sm font-semibold text-slate-600">📅 Retención de backups (días)</label>
                    <input type="number" name="backup_retention_days" value="{{ $data['backup_retention_days'] }}" 
                           min="1" max="365"
                           class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
                    <p class="text-xs text-slate-500 mt-1">Eliminar backups más antiguos</p>
                    @error('backup_retention_days') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Botón crear backup --}}
            <div class="mb-4">
                <form action="{{ route('panel.config.critica.backup.create') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700 transition">
                        ➕ Crear backup ahora
                    </button>
                </form>
            </div>

            {{-- Lista de backups --}}
            <div class="border border-slate-200 rounded-xl overflow-hidden">
                <div class="bg-slate-50 px-4 py-2 border-b border-slate-200">
                    <h3 class="text-sm font-semibold text-slate-600">📁 Backups disponibles ({{ count($backups) }})</h3>
                </div>
                
                @if(count($backups) > 0)
                    <div class="divide-y divide-slate-100 max-h-64 overflow-y-auto">
                        @foreach($backups as $backup)
                            <div class="flex items-center justify-between px-4 py-3 hover:bg-slate-50">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-slate-800 truncate">{{ $backup['filename'] }}</p>
                                    <p class="text-xs text-slate-500">{{ $backup['size'] }} · {{ $backup['age'] }}</p>
                                </div>
                                <div class="flex items-center gap-2 ml-4">
                                    <a href="{{ route('panel.config.critica.backup.download', $backup['filename']) }}" 
                                       class="px-3 py-1 text-xs font-bold text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 transition">
                                        ⬇️ Descargar
                                    </a>
                                    <form action="{{ route('panel.config.critica.backup.delete', $backup['filename']) }}" 
                                          method="POST" 
                                          onsubmit="return confirm('¿Eliminar este backup?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="px-3 py-1 text-xs font-bold text-rose-600 bg-rose-50 rounded-lg hover:bg-rose-100 transition">
                                            🗑️ Eliminar
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="px-4 py-6 text-center text-slate-500 text-sm">
                        No hay backups disponibles. Crea uno nuevo.
                    </div>
                @endif
            </div>
        </div>

        {{-- Botón guardar --}}
        <div>
            <button type="submit"
                    class="w-full sm:w-auto px-6 py-3 rounded-2xl bg-rose-600 text-white font-extrabold hover:bg-rose-700 transition">
                🔐 Guardar configuración crítica
            </button>
        </div>
    </form>

</div>
@endsection
