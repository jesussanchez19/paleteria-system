@extends('layouts.app')

@section('title', 'Configuración crítica')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="flex items-end justify-between">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold">Configuración crítica 🔐</h1>
            <p class="text-slate-600">Solo administradores. Ajustes sensibles del sistema.</p>
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
        <h2 class="text-lg font-extrabold mb-4 text-slate-800">🔧 Herramientas de Mantenimiento</h2>
        
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
                            <span class="font-medium capitalize">{{ str_replace('_', ' ', $service) }}:</span>
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
                    <span class="font-bold text-slate-700">Limpiar Caché</span>
                    <span class="text-xs text-slate-500 block">Config, vistas, rutas</span>
                </button>
            </form>

            {{-- Probar conexiones --}}
            <form method="POST" action="{{ route('panel.config.critica.test-connections') }}" class="block">
                @csrf
                <button type="submit" class="w-full p-4 rounded-xl border-2 border-dashed border-slate-300 hover:border-emerald-400 hover:bg-emerald-50 transition text-center">
                    <span class="text-2xl block mb-1">🔌</span>
                    <span class="font-bold text-slate-700">Probar Conexiones</span>
                    <span class="text-xs text-slate-500 block">BD, APIs externas</span>
                </button>
            </form>

            {{-- Limpiar logs antiguos --}}
            <form method="POST" action="{{ route('panel.config.critica.clean-logs') }}" 
                  onsubmit="return confirm('¿Eliminar registros de auditoría con más de 90 días?')" class="block">
                @csrf
                <input type="hidden" name="days" value="90">
                <button type="submit" class="w-full p-4 rounded-xl border-2 border-dashed border-slate-300 hover:border-amber-400 hover:bg-amber-50 transition text-center">
                    <span class="text-2xl block mb-1">🗑️</span>
                    <span class="font-bold text-slate-700">Limpiar Logs</span>
                    <span class="text-xs text-slate-500 block">Más de 90 días</span>
                </button>
            </form>
        </div>
    </div>

    {{-- Gestión del Gerente --}}
    @if($gerente)
    <div class="bg-white border border-indigo-200 rounded-2xl p-5 shadow-sm">
        <h2 class="text-lg font-extrabold mb-4 text-slate-800">👤 Gestión del Gerente</h2>
        
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
                    <label class="text-sm font-bold text-slate-700">📝 Nombre</label>
                    <input type="text" name="gerente_name" value="{{ old('gerente_name', $gerente->name) }}"
                           class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2" required>
                    @error('gerente_name') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="text-sm font-bold text-slate-700">📧 Email</label>
                    <input type="email" name="gerente_email" value="{{ old('gerente_email', $gerente->email) }}"
                           class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2" required>
                    @error('gerente_email') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="text-sm font-bold text-slate-700">🔑 Nueva contraseña</label>
                    <input type="password" name="gerente_password" 
                           class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2"
                           placeholder="Dejar vacío para no cambiar">
                    <p class="text-xs text-slate-500 mt-1">Mínimo 8 caracteres</p>
                    @error('gerente_password') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="text-sm font-bold text-slate-700">🔑 Confirmar contraseña</label>
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
    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
        <p class="text-sm text-amber-800">No hay un gerente registrado en el sistema.</p>
    </div>
    @endif

    <form method="POST" action="{{ route('panel.config.critica.update') }}" class="space-y-6">
        @csrf

        {{-- APIs y Servicios Externos --}}
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <h2 class="text-lg font-extrabold mb-4 text-slate-800">🔗 APIs y Servicios Externos</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-bold text-slate-700">🤖 API Key de IA (Gemini)</label>
                    <input type="password" name="ai_api_key" value="{{ $data['ai_api_key'] }}"
                           class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2" 
                           placeholder="AIzaSy...">
                    <p class="text-xs text-slate-500 mt-1">Para sugerencias inteligentes</p>
                    @error('ai_api_key') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="text-sm font-bold text-slate-700">🌤️ API Key del Clima</label>
                    <input type="password" name="weather_api_key" value="{{ $data['weather_api_key'] }}"
                           class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2" 
                           placeholder="abc123...">
                    <p class="text-xs text-slate-500 mt-1">OpenWeatherMap o similar</p>
                    @error('weather_api_key') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Estado del Sistema --}}
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <h2 class="text-lg font-extrabold mb-4 text-slate-800">⚡ Estado del Sistema</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-bold text-slate-700">🛠️ Modo mantenimiento</label>
                    <select name="maintenance_mode" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
                        <option value="0" {{ $data['maintenance_mode'] == '0' ? 'selected' : '' }}>❌ Desactivado</option>
                        <option value="1" {{ $data['maintenance_mode'] == '1' ? 'selected' : '' }}>✅ Activado</option>
                    </select>
                    <p class="text-xs text-slate-500 mt-1">Solo admins pueden entrar</p>
                    @error('maintenance_mode') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="text-sm font-bold text-slate-700">💰 Ventas habilitadas</label>
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
            <h2 class="text-lg font-extrabold mb-4 text-slate-800">🔒 Seguridad</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="text-sm font-bold text-slate-700">🚫 Máx intentos login</label>
                    <input type="number" name="max_login_attempts" value="{{ $data['max_login_attempts'] }}" 
                           min="1" max="20"
                           class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
                    <p class="text-xs text-slate-500 mt-1">Antes de bloquear</p>
                    @error('max_login_attempts') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="text-sm font-bold text-slate-700">⏱️ Timeout sesión (min)</label>
                    <input type="number" name="session_timeout" value="{{ $data['session_timeout'] }}" 
                           min="5" max="480"
                           class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
                    <p class="text-xs text-slate-500 mt-1">Inactividad máxima</p>
                    @error('session_timeout') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="text-sm font-bold text-slate-700">🔑 Cambiar contraseña (días)</label>
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
            <h2 class="text-lg font-extrabold mb-4 text-slate-800">💵 Límites de Ventas</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-bold text-slate-700">💳 Monto máx sin autorización ($)</label>
                    <input type="number" name="max_sale_without_auth" value="{{ $data['max_sale_without_auth'] }}" 
                           min="0" step="100"
                           class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
                    <p class="text-xs text-slate-500 mt-1">Ventas mayores requieren supervisor</p>
                    @error('max_sale_without_auth') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="text-sm font-bold text-slate-700">🏷️ Descuento máximo (%)</label>
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
            <h2 class="text-lg font-extrabold mb-4 text-slate-800">💾 Respaldos</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-bold text-slate-700">🔄 Backup automático</label>
                    <select name="auto_backup_enabled" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
                        <option value="0" {{ $data['auto_backup_enabled'] == '0' ? 'selected' : '' }}>❌ Desactivado</option>
                        <option value="1" {{ $data['auto_backup_enabled'] == '1' ? 'selected' : '' }}>✅ Activado</option>
                    </select>
                    <p class="text-xs text-slate-500 mt-1">Respaldo diario automático</p>
                    @error('auto_backup_enabled') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="text-sm font-bold text-slate-700">📅 Retención de backups (días)</label>
                    <input type="number" name="backup_retention_days" value="{{ $data['backup_retention_days'] }}" 
                           min="1" max="365"
                           class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
                    <p class="text-xs text-slate-500 mt-1">Eliminar backups más antiguos</p>
                    @error('backup_retention_days') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>
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
