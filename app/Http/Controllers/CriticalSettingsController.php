<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\User;
use App\Models\Sale;
use App\Models\Product;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules;
use App\Services\CloudinaryService;
use App\Services\WhatsAppService;
use App\Mail\GerenteVerificationCodeMail;

class CriticalSettingsController extends Controller
{
    public function edit()
    {
        $data = [
            // IA y APIs
            'ai_api_key' => app_setting('ai_api_key'),
            'weather_api_key' => app_setting('weather_api_key'),
            
            // Sistema
            'maintenance_mode' => app_setting('maintenance_mode', '0'),
            'sales_enabled' => app_setting('sales_enabled', '1'),
            
            // Seguridad
            'max_login_attempts' => app_setting('max_login_attempts', '5'),
            'session_timeout' => app_setting('session_timeout', '120'),
            'force_password_change_days' => app_setting('force_password_change_days', '0'),
            
            // Límites de ventas
            'max_sale_without_auth' => app_setting('max_sale_without_auth', '5000'),
            'max_discount_percent' => app_setting('max_discount_percent', '50'),
            
            // Backup
            'auto_backup_enabled' => app_setting('auto_backup_enabled', '0'),
            'backup_retention_days' => app_setting('backup_retention_days', '30'),
        ];

        // Obtener el gerente (solo hay uno)
        $gerente = User::where('role', 'gerente')->first();

        // Estadísticas del sistema
        $stats = [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'db_connection' => config('database.default'),
            'total_usuarios' => User::count(),
            'vendedores_activos' => User::where('role', 'vendedor')->where('is_active', true)->count(),
            'total_productos' => Product::count(),
            'productos_activos' => Product::where('is_active', true)->count(),
            'productos_bajo_stock' => Product::whereColumn('stock', '<=', DB::raw('COALESCE((SELECT value::int FROM settings WHERE key = \'low_stock_threshold\'), 5)'))->count(),
            'total_ventas' => Sale::count(),
            'ventas_hoy' => Sale::whereDate('created_at', today())->count(),
            'ventas_mes' => Sale::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count(),
            'ingresos_hoy' => Sale::whereDate('created_at', today())->sum('total'),
            'ingresos_mes' => Sale::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->sum('total'),
            'logs_auditoria' => AuditLog::count(),
            'ultimo_log' => AuditLog::latest()->first()?->created_at?->diffForHumans() ?? 'Sin registros',
            'db_size' => $this->getDatabaseSize(),
        ];

        // Obtener lista de backups
        $backups = $this->getBackupsList();

        $storagePublicExists = is_dir(storage_path('app/public'));
        $publicStorageExposed = is_link(public_path('storage')) || is_dir(public_path('storage'));
        $storageReady = $storagePublicExists && $publicStorageExposed;

        $systemConnections = [
            [
                'key' => 'database',
                'label' => 'Base de datos',
                'type' => 'Interna',
                'configured' => !empty(config('database.default')),
                'details' => strtoupper((string) config('database.default', 'N/A')),
            ],
            [
                'key' => 'groq_api',
                'label' => 'Groq API',
                'type' => 'API externa',
                'configured' => !empty(config('services.groq.key')),
                'details' => config('services.groq.model', 'Sin modelo'),
            ],
            [
                'key' => 'mail',
                'label' => 'Correo SMTP',
                'type' => 'Servicio externo',
                'configured' => !empty(config('mail.default')) && config('mail.default') !== 'log',
                'details' => strtoupper((string) config('mail.default', 'LOG')),
            ],
            [
                'key' => 'openweather_api',
                'label' => 'OpenWeather API',
                'type' => 'API externa',
                'configured' => !empty(config('services.openweather.key')),
                'details' => config('services.openweather.city', 'Sin ciudad'),
            ],
            [
                'key' => 'whatsapp',
                'label' => 'WhatsApp API',
                'type' => 'API externa',
                'configured' => !empty(config('services.whatsapp.token')) && !empty(config('services.whatsapp.phone_number_id')),
                'details' => !empty(config('services.whatsapp.phone_number_id')) ? 'Phone Number ID configurado' : 'Sin configurar',
            ],
            [
                'key' => 'cloudinary',
                'label' => 'Cloudinary',
                'type' => 'Almacenamiento externo',
                'configured' => CloudinaryService::isConfigured(),
                'details' => CloudinaryService::isConfigured() ? 'Configurado por CLOUDINARY_URL' : 'Sin configurar',
            ],
            [
                'key' => 'public_storage',
                'label' => 'Storage público',
                'type' => 'Filesystem',
                'configured' => $storageReady,
                'details' => $storageReady
                    ? 'Listo para servir archivos públicos'
                    : ($storagePublicExists ? 'Existe storage/app/public pero falta exponer public/storage' : 'Falta storage/app/public y public/storage'),
            ],
        ];

        return view('panel.config-critica', compact('data', 'gerente', 'stats', 'backups', 'systemConnections'));
    }

    private function getDatabaseSize()
    {
        try {
            $dbName = config('database.connections.pgsql.database');
            $result = DB::select("SELECT pg_size_pretty(pg_database_size(?)) as size", [$dbName]);
            return $result[0]->size ?? 'N/A';
        } catch (\Exception $e) {
            return 'N/A';
        }
    }

    public function clearCache()
    {
        try {
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('view:clear');
            Artisan::call('route:clear');
            
            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'cache.cleared',
                'module' => 'sistema',
                'entity_type' => 'System',
                'entity_id' => null,
                'meta' => ['_entity_name' => 'Caché del sistema', 'acción' => 'Limpieza completa'],
            ]);

            return back()->with('success_tools', 'Caché limpiado correctamente.');
        } catch (\Exception $e) {
            return back()->with('error_tools', 'Error al limpiar caché: ' . $e->getMessage());
        }
    }

    public function cleanOldLogs(Request $request)
    {
        $days = $request->input('days', 90);
        
        try {
            $deleted = AuditLog::where('created_at', '<', now()->subDays($days))->delete();
            
            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'logs.cleaned',
                'module' => 'sistema',
                'entity_type' => 'AuditLog',
                'entity_id' => null,
                'meta' => [
                    '_entity_name' => 'Limpieza de logs',
                    'eliminados' => $deleted,
                    'días_antigüedad' => $days,
                ],
            ]);

            return back()->with('success_tools', "Se eliminaron $deleted registros de auditoría antiguos.");
        } catch (\Exception $e) {
            return back()->with('error_tools', 'Error al limpiar logs: ' . $e->getMessage());
        }
    }

    public function viewLogs(Request $request)
    {
        $lines = (int) $request->integer('lines', 200);
        $lines = max(50, min($lines, 1000));

        $logPath = storage_path('logs/laravel.log');
        $exists = File::exists($logPath);
        $logLines = $exists ? $this->tailLogFile($logPath, $lines) : [];

        $logInfo = [
            'exists' => $exists,
            'path' => $logPath,
            'size' => $exists ? $this->formatBytes((int) File::size($logPath)) : '0 B',
            'updated_at' => $exists ? date('d/m/Y H:i:s', File::lastModified($logPath)) : null,
            'line_count' => count($logLines),
            'requested_lines' => $lines,
        ];

        return view('panel.system-logs', compact('logLines', 'logInfo'));
    }

    private function tailLogFile(string $path, int $lines): array
    {
        $file = new \SplFileObject($path, 'r');
        $file->seek(PHP_INT_MAX);
        $lastLine = $file->key();
        $startLine = max(0, $lastLine - $lines);
        $result = [];

        $file->seek($startLine);

        while (! $file->eof()) {
            $line = rtrim((string) $file->current(), "\r\n");

            if ($line !== '') {
                $result[] = $line;
            }

            $file->next();
        }

        return $result;
    }

    public function testConnections()
    {
        $results = [];
        
        // Test DB
        try {
            DB::connection()->getPdo();
            $results['database'] = ['label' => 'Base de datos', 'status' => 'ok', 'message' => 'Conexión exitosa'];
        } catch (\Exception $e) {
            $results['database'] = ['label' => 'Base de datos', 'status' => 'error', 'message' => $e->getMessage()];
        }
        
        // Test API Groq
        $aiKey = config('services.groq.key') ?: app_setting('ai_api_key');
        if ($aiKey) {
            try {
                $response = \Illuminate\Support\Facades\Http::timeout(5)
                    ->withHeaders([
                        'Authorization' => "Bearer {$aiKey}",
                        'Content-Type' => 'application/json',
                    ])
                    ->post('https://api.groq.com/openai/v1/chat/completions', [
                        'model' => config('services.groq.model', 'llama-3.3-70b-versatile'),
                        'messages' => [
                            ['role' => 'user', 'content' => 'Responde solo OK'],
                        ],
                        'max_tokens' => 10,
                    ]);

                $results['groq_api'] = $response->successful()
                    ? ['label' => 'Groq API', 'status' => 'ok', 'message' => 'API funcionando']
                    : ['label' => 'Groq API', 'status' => 'error', 'message' => 'Error ' . $response->status()];
            } catch (\Exception $e) {
                $results['groq_api'] = ['label' => 'Groq API', 'status' => 'error', 'message' => 'Timeout o error de conexión'];
            }
        } else {
            $results['groq_api'] = ['label' => 'Groq API', 'status' => 'warning', 'message' => 'No configurada'];
        }

        $mailConfigured = !empty(config('mail.default')) && config('mail.default') !== 'log';
        $results['mail'] = $mailConfigured
            ? ['label' => 'Correo SMTP', 'status' => 'ok', 'message' => 'Mailer activo: ' . strtoupper((string) config('mail.default'))]
            : ['label' => 'Correo SMTP', 'status' => 'warning', 'message' => 'MAIL_MAILER sigue en LOG o sin configurar'];
        
        // Test API OpenWeather
        $weatherKey = config('services.openweather.key') ?: app_setting('weather_api_key');
        if ($weatherKey) {
            try {
                $response = \Illuminate\Support\Facades\Http::timeout(5)
                    ->get('https://api.openweathermap.org/data/2.5/weather', [
                        'q' => config('services.openweather.city', 'Culiacan'),
                        'appid' => $weatherKey,
                        'units' => config('services.openweather.units', 'metric'),
                        'lang' => config('services.openweather.lang', 'es'),
                    ]);

                $results['openweather_api'] = $response->successful()
                    ? ['label' => 'OpenWeather API', 'status' => 'ok', 'message' => 'API funcionando']
                    : ['label' => 'OpenWeather API', 'status' => 'error', 'message' => 'Error ' . $response->status()];
            } catch (\Exception $e) {
                $results['openweather_api'] = ['label' => 'OpenWeather API', 'status' => 'error', 'message' => 'Timeout o error de conexión'];
            }
        } else {
            $results['openweather_api'] = ['label' => 'OpenWeather API', 'status' => 'warning', 'message' => 'No configurada'];
        }

        // Test Cloudinary
        if (CloudinaryService::isConfigured()) {
            $results['cloudinary'] = ['label' => 'Cloudinary', 'status' => 'ok', 'message' => 'Configuración detectada'];
        } else {
            $results['cloudinary'] = ['label' => 'Cloudinary', 'status' => 'warning', 'message' => 'No configurada'];
        }

        $results['whatsapp'] = (!empty(config('services.whatsapp.token')) && !empty(config('services.whatsapp.phone_number_id')))
            ? ['label' => 'WhatsApp API', 'status' => 'ok', 'message' => 'Configuración detectada']
            : ['label' => 'WhatsApp API', 'status' => 'warning', 'message' => 'Falta token o phone number id'];

        // Test Storage público
        $storageReady = is_dir(storage_path('app/public')) && (is_link(public_path('storage')) || is_dir(public_path('storage')));
        $results['public_storage'] = $storageReady
            ? ['label' => 'Storage público', 'status' => 'ok', 'message' => 'Disponible']
            : ['label' => 'Storage público', 'status' => 'warning', 'message' => 'Falta enlace o carpeta pública'];

        return back()->with('connection_results', $results);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            // IA y APIs
            'ai_api_key' => ['nullable', 'string', 'max:200'],
            'weather_api_key' => ['nullable', 'string', 'max:200'],
            
            // Sistema
            'maintenance_mode' => ['required', 'boolean'],
            'sales_enabled' => ['required', 'boolean'],
            
            // Seguridad
            'max_login_attempts' => ['required', 'integer', 'min:1', 'max:20'],
            'session_timeout' => ['required', 'integer', 'min:5', 'max:480'],
            'force_password_change_days' => ['required', 'integer', 'min:0', 'max:365'],
            
            // Límites de ventas
            'max_sale_without_auth' => ['required', 'numeric', 'min:0'],
            'max_discount_percent' => ['required', 'integer', 'min:0', 'max:100'],
            
            // Backup
            'auto_backup_enabled' => ['required', 'boolean'],
            'backup_retention_days' => ['required', 'integer', 'min:1', 'max:365'],
        ]);

        // Capturar valores anteriores
        $oldValues = [];
        foreach (array_keys($validated) as $key) {
            $oldValues[$key] = app_setting($key, '');
        }

        foreach ($validated as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => (string) $value]
            );
        }

        // Detectar cambios para auditoría
        $changes = [];
        $sensitiveKeys = ['ai_api_key', 'weather_api_key'];
        $booleanKeys = ['maintenance_mode', 'sales_enabled', 'auto_backup_enabled'];
        $labels = [
            'ai_api_key' => 'API Key IA',
            'weather_api_key' => 'API Key Clima',
            'maintenance_mode' => 'Modo mantenimiento',
            'sales_enabled' => 'Ventas habilitadas',
            'max_login_attempts' => 'Máx. intentos login',
            'session_timeout' => 'Timeout sesión (min)',
            'force_password_change_days' => 'Forzar cambio contraseña (días)',
            'max_sale_without_auth' => 'Venta máx. sin auth',
            'max_discount_percent' => 'Descuento máx. %',
            'auto_backup_enabled' => 'Backup automático',
            'backup_retention_days' => 'Retención backup (días)',
        ];
        
        // Campos a ignorar en auditoría
        $ignoreFields = [];
        
        foreach ($validated as $key => $newValue) {
            // Ignorar campos de coordenadas
            if (in_array($key, $ignoreFields)) {
                continue;
            }
            
            $oldValue = $oldValues[$key] ?? '';
            $label = $labels[$key] ?? $key;
            
            // No mostrar API keys en los cambios por seguridad
            if (in_array($key, $sensitiveKeys)) {
                if ((string) $oldValue !== (string) $newValue) {
                    $changes[$label] = '***actualizada***';
                }
            } elseif ((string) $oldValue !== (string) $newValue) {
                // Formatear valores booleanos
                if (in_array($key, $booleanKeys)) {
                    $oldDisplay = $oldValue == '1' ? 'Sí' : 'No';
                    $newDisplay = $newValue == '1' ? 'Sí' : 'No';
                } else {
                    $oldDisplay = $oldValue ?: '(vacío)';
                    $newDisplay = $newValue ?: '(vacío)';
                }
                $changes[$label] = $oldDisplay . ' → ' . $newDisplay;
            }
        }

        // Auditoría con detalles de cambios
        if (!empty($changes)) {
            \App\Models\AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'critical.settings.updated',
                'module' => 'config',
                'entity_type' => 'Settings',
                'entity_id' => null,
                'meta' => array_merge(['_entity_name' => 'Configuración crítica'], $changes),
            ]);
        }

        return back()->with('success', 'Configuración crítica actualizada.');
    }

    public function updateGerente(Request $request)
    {
        $gerente = User::where('role', 'gerente')->first();
        
        if (!$gerente) {
            return back()->with('error', 'No se encontró un gerente en el sistema.');
        }

        $rules = [
            'gerente_name' => ['required', 'string', 'max:255'],
            'gerente_email' => ['required', 'email', 'max:255', 'unique:users,email,' . $gerente->id],
            'gerente_phone' => ['nullable', 'string', 'max:20'],
        ];

        // Si se proporciona contraseña, validarla
        if ($request->filled('gerente_password')) {
            $rules['gerente_password'] = ['required', 'min:8', 'confirmed'];
        }

        $validated = $request->validate($rules);

        $emailChanged = mb_strtolower($validated['gerente_email']) !== mb_strtolower($gerente->email);
        $phoneChanged = ($validated['gerente_phone'] ?? null) !== ($gerente->phone ?? null);

        if ($emailChanged) {
            $request->validate([
                'gerente_email_verification_code' => ['required', 'digits:6'],
            ]);

            $emailCacheKey = $this->gerenteEmailVerificationCacheKey($validated['gerente_email']);
            $emailVerificationData = Cache::get($emailCacheKey);

            if (!$emailVerificationData) {
                return back()->withInput()->with('error', 'Primero verifica el nuevo correo del gerente.');
            }

            if (($emailVerificationData['email'] ?? null) !== $validated['gerente_email']) {
                return back()->withInput()->with('error', 'El correo cambió después de enviar el código. Vuelve a solicitarlo.');
            }

            if (!Hash::check($request->input('gerente_email_verification_code'), $emailVerificationData['code_hash'] ?? '')) {
                return back()->withInput()->with('error', 'El código de verificación del correo no es válido.');
            }
        }

        if ($phoneChanged && !empty($validated['gerente_phone'])) {
            $request->validate([
                'gerente_phone_verification_code' => ['required', 'digits:6'],
            ]);

            $phoneCacheKey = $this->gerentePhoneVerificationCacheKey($validated['gerente_phone']);
            $phoneVerificationData = Cache::get($phoneCacheKey);

            if (!$phoneVerificationData) {
                return back()->withInput()->with('error', 'Primero verifica el nuevo número por WhatsApp.');
            }

            if (($phoneVerificationData['phone'] ?? null) !== $validated['gerente_phone']) {
                return back()->withInput()->with('error', 'El número cambió después de enviar el código. Vuelve a solicitarlo.');
            }

            if (!Hash::check($request->input('gerente_phone_verification_code'), $phoneVerificationData['code_hash'] ?? '')) {
                return back()->withInput()->with('error', 'El código de verificación de WhatsApp no es válido.');
            }
        }

        // Capturar valores anteriores
        $oldName = $gerente->name;
        $oldEmail = $gerente->email;
        $oldPhone = $gerente->phone;

        // Actualizar datos
        $gerente->name = $validated['gerente_name'];
        $gerente->email = $validated['gerente_email'];
        $gerente->phone = $validated['gerente_phone'] ?? null;

        $changes = [];
        if ($oldName !== $gerente->name) {
            $changes['Nombre'] = $oldName . ' → ' . $gerente->name;
        }
        if ($oldEmail !== $gerente->email) {
            $changes['Email'] = $oldEmail . ' → ' . $gerente->email;
        }
        if (($oldPhone ?? '') !== ($gerente->phone ?? '')) {
            $changes['Teléfono'] = ($oldPhone ?: '(vacío)') . ' → ' . ($gerente->phone ?: '(vacío)');
        }

        if ($request->filled('gerente_password')) {
            $gerente->password = Hash::make($validated['gerente_password']);
            $changes['Contraseña'] = '***actualizada***';
        }

        $gerente->save();

        if ($emailChanged) {
            Cache::forget($this->gerenteEmailVerificationCacheKey($validated['gerente_email']));
        }

        if ($phoneChanged && !empty($validated['gerente_phone'])) {
            Cache::forget($this->gerentePhoneVerificationCacheKey($validated['gerente_phone']));
        }

        // Auditoría
        if (!empty($changes)) {
            \App\Models\AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'gerente.updated',
                'module' => 'usuarios',
                'entity_type' => 'User',
                'entity_id' => $gerente->id,
                'meta' => array_merge(['_entity_name' => $gerente->name], $changes),
            ]);
        }

        return back()->with('success_gerente', 'Datos del gerente actualizados correctamente.');
    }

    public function sendGerenteVerificationCode(Request $request)
    {
        $gerente = User::where('role', 'gerente')->first();

        if (config('mail.default') === 'log') {
            return back()
                ->withInput()
                ->with('error', 'El correo no está configurado para envío real. Configura Gmail SMTP antes de enviar el código.');
        }

        $validated = $request->validate([
            'gerente_name' => ['required', 'string', 'max:255'],
            'gerente_email' => ['required', 'email', 'max:255', 'unique:users,email,' . ($gerente?->id ?? 'NULL')],
        ]);

        $code = (string) random_int(100000, 999999);
        $cacheKey = $this->gerenteEmailVerificationCacheKey($validated['gerente_email']);

        Cache::put($cacheKey, [
            'email' => $validated['gerente_email'],
            'code_hash' => Hash::make($code),
        ], now()->addMinutes(10));

        try {
            Mail::to($validated['gerente_email'])->send(
                new GerenteVerificationCodeMail($validated['gerente_name'], $code)
            );

            return back()
                ->withInput()
                ->with('success_gerente', 'Código enviado al correo del gerente. Vigencia: 10 minutos.');
        } catch (\Throwable $e) {
            Cache::forget($cacheKey);

            return back()
                ->withInput()
                ->with('error', 'No se pudo enviar el código por correo: ' . $e->getMessage());
        }
    }

    public function sendGerentePhoneVerificationCode(Request $request, WhatsAppService $whatsAppService)
    {
        if (!WhatsAppService::isConfigured()) {
            return back()
                ->withInput()
                ->with('error', 'WhatsApp no está configurado todavía. Configura el token y el phone number id antes de enviar el código.');
        }

        $validated = $request->validate([
            'gerente_name' => ['required', 'string', 'max:255'],
            'gerente_phone' => ['required', 'string', 'max:20'],
        ]);

        $code = (string) random_int(100000, 999999);
        $cacheKey = $this->gerentePhoneVerificationCacheKey($validated['gerente_phone']);

        Cache::put($cacheKey, [
            'phone' => $validated['gerente_phone'],
            'code_hash' => Hash::make($code),
        ], now()->addMinutes(10));

        $message = "Hola {$validated['gerente_name']}, tu código de verificación para gerente en Creamyx es: {$code}. Vigencia: 10 minutos.";

        if (!$whatsAppService->sendText($validated['gerente_phone'], $message)) {
            Cache::forget($cacheKey);

            return back()
                ->withInput()
                ->with('error', 'No se pudo enviar el código por WhatsApp. Revisa la configuración y el formato del número.');
        }

        return back()
            ->withInput()
            ->with('success_gerente', 'Código enviado por WhatsApp. Vigencia: 10 minutos.');
    }

    /**
     * Obtener lista de backups disponibles
     */
    private function getBackupsList(): array
    {
        $dir = storage_path('backups');
        $backups = [];

        if (!is_dir($dir)) {
            return $backups;
        }

        $files = glob($dir . DIRECTORY_SEPARATOR . '*.sql');
        
        foreach ($files as $file) {
            $backups[] = [
                'filename' => basename($file),
                'size' => $this->formatBytes(filesize($file)),
                'date' => date('Y-m-d H:i:s', filemtime($file)),
                'age' => now()->diffForHumans(\Carbon\Carbon::createFromTimestamp(filemtime($file))),
            ];
        }

        // Ordenar por fecha descendente (más reciente primero)
        usort($backups, fn($a, $b) => strtotime($b['date']) - strtotime($a['date']));

        return $backups;
    }

    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Crear nuevo backup
     */
    public function createBackup()
    {
        try {
            Artisan::call('backup:db');
            
            return back()->with('success_backup', 'Backup creado correctamente.');
        } catch (\Exception $e) {
            return back()->with('error_backup', 'Error al crear backup: ' . $e->getMessage());
        }
    }

    /**
     * Descargar backup
     */
    public function downloadBackup(string $filename)
    {
        // Sanitizar nombre de archivo
        $filename = basename($filename);
        $path = storage_path('backups/' . $filename);

        if (!file_exists($path) || !str_ends_with($filename, '.sql')) {
            abort(404, 'Backup no encontrado.');
        }

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'backup.downloaded',
            'module' => 'sistema',
            'entity_type' => 'Backup',
            'entity_id' => null,
            'meta' => ['_entity_name' => $filename],
        ]);

        return response()->download($path);
    }

    /**
     * Eliminar backup
     */
    public function deleteBackup(string $filename)
    {
        // Sanitizar nombre de archivo
        $filename = basename($filename);
        $path = storage_path('backups/' . $filename);

        if (!file_exists($path) || !str_ends_with($filename, '.sql')) {
            return back()->with('error_backup', 'Backup no encontrado.');
        }

        @unlink($path);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'backup.deleted',
            'module' => 'sistema',
            'entity_type' => 'Backup',
            'entity_id' => null,
            'meta' => ['_entity_name' => $filename],
        ]);

        return back()->with('success_backup', 'Backup eliminado correctamente.');
    }

    public function storeGerente(Request $request)
    {
        // Verificar que no exista ya un gerente
        if (User::where('role', 'gerente')->exists()) {
            return back()->with('error', 'Ya existe un gerente en el sistema.');
        }

        $validated = $request->validate([
            'gerente_name' => ['required', 'string', 'max:255'],
            'gerente_email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'gerente_phone' => ['required', 'string', 'max:20'],
            'gerente_password' => ['required', 'min:8', 'confirmed'],
            'gerente_email_verification_code' => ['required', 'digits:6'],
            'gerente_phone_verification_code' => ['required', 'digits:6'],
        ]);

        $emailCacheKey = $this->gerenteEmailVerificationCacheKey($validated['gerente_email']);
        $phoneCacheKey = $this->gerentePhoneVerificationCacheKey($validated['gerente_phone']);
        $emailVerificationData = Cache::get($emailCacheKey);
        $phoneVerificationData = Cache::get($phoneCacheKey);

        if (!$emailVerificationData) {
            return back()->withInput()->with('error', 'Primero envía el código de verificación al correo del gerente.');
        }

        if (($emailVerificationData['email'] ?? null) !== $validated['gerente_email']) {
            return back()->withInput()->with('error', 'El correo cambió después de enviar el código. Vuelve a solicitarlo.');
        }

        if (!Hash::check($validated['gerente_email_verification_code'], $emailVerificationData['code_hash'] ?? '')) {
            return back()->withInput()->with('error', 'El código de verificación del correo no es válido.');
        }

        if (!$phoneVerificationData) {
            return back()->withInput()->with('error', 'Primero envía el código de verificación por WhatsApp.');
        }

        if (($phoneVerificationData['phone'] ?? null) !== $validated['gerente_phone']) {
            return back()->withInput()->with('error', 'El número cambió después de enviar el código. Vuelve a solicitarlo.');
        }

        if (!Hash::check($validated['gerente_phone_verification_code'], $phoneVerificationData['code_hash'] ?? '')) {
            return back()->withInput()->with('error', 'El código de verificación de WhatsApp no es válido.');
        }

        $gerente = User::create([
            'name' => $validated['gerente_name'],
            'email' => $validated['gerente_email'],
            'phone' => $validated['gerente_phone'],
            'password' => Hash::make($validated['gerente_password']),
            'role' => 'gerente',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        Cache::forget($emailCacheKey);
        Cache::forget($phoneCacheKey);

        // Auditoría
        \App\Models\AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'gerente.created',
            'module' => 'usuarios',
            'entity_type' => 'User',
            'entity_id' => $gerente->id,
            'meta' => ['_entity_name' => $gerente->name, 'email' => $gerente->email, 'teléfono' => $gerente->phone],
        ]);

        return back()->with('success_gerente', 'Gerente creado correctamente.');
    }

    private function gerenteEmailVerificationCacheKey(string $email): string
    {
        return 'gerente_verification_email:' . sha1(mb_strtolower(trim($email)));
    }

    private function gerentePhoneVerificationCacheKey(string $phone): string
    {
        return 'gerente_verification_phone:' . sha1(preg_replace('/\D+/', '', $phone));
    }
}
