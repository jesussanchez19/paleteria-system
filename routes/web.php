<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\VendedorController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\DailyReportController;
use App\Http\Controllers\DashboardStatsController;
use App\Services\CloudinaryService;

// Health check para Railway (no requiere nada)
Route::get('/health', fn () => response('ok', 200));

// Diagnóstico de Cloudinary (temporal - eliminar después)
Route::get('/cloudinary-check', function () {
    $configUrl = config('cloudinary.cloud_url');
    $envUrl = env('CLOUDINARY_URL');
    $isConfigured = CloudinaryService::isConfigured();
    
    return response()->json([
        'config_url_exists' => !empty($configUrl),
        'config_url_preview' => $configUrl ? substr($configUrl, 0, 30) . '...' : null,
        'env_url_exists' => !empty($envUrl),
        'env_url_preview' => $envUrl ? substr($envUrl, 0, 30) . '...' : null,
        'is_configured' => $isConfigured,
    ]);
});

// Público - redirige según cookie de computadora de trabajo
Route::get('/', function () {
    // Si tiene cookie de computadora de trabajo, va al login
    if (request()->cookie('work_computer') === 'true') {
        return redirect()->route('login');
    }
    return redirect()->route('catalogo.index');
});

// Marcar esta computadora como de trabajo (guarda cookie por 1 año)
Route::get('/marcar-pc-trabajo', function () {
    return redirect()->route('panel.config.critica')
        ->withCookie(cookie()->forever('work_computer', 'true'));
})->middleware(['auth', 'role:admin'])->name('marcar.pc.trabajo');

// Desmarcar esta computadora como de trabajo
Route::get('/desmarcar-pc-trabajo', function () {
    return redirect()->route('panel.config.critica')
        ->withCookie(cookie()->forget('work_computer'));
})->middleware(['auth', 'role:admin'])->name('desmarcar.pc.trabajo');

// Acceso directo para empleados (computadora de trabajo)
Route::get('/sistema', fn () => redirect()->route('login'))->name('sistema');

Route::get('/catalogo', [CatalogController::class, 'index'])
    ->name('catalogo.index');

// Auth (Breeze)
require __DIR__.'/auth.php';

// Básico autenticado (Breeze)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Redirección post-login según rol
Route::get('/redirect-after-login', function () {
    /** @var \App\Models\User|null $user */
    $user = \Illuminate\Support\Facades\Auth::user();
    if (!$user) {
        return redirect()->route('login');
    }
    if ($user->isVendedor()) {
        return redirect()->route('pos.index');
    }
    if ($user->isAdmin()) {
        return redirect()->route('panel.config.critica');
    }
    if ($user->isGerente()) {
        return redirect()->route('panel.index');
    }
    return redirect('/');
})->middleware('auth')->name('redirect.after.login');

// POS (vendedor y gerente)
Route::middleware(['auth', 'role:vendedor,gerente'])->group(function () {
    Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
    Route::post('/pos/checkout', [SaleController::class, 'store'])->name('pos.checkout');
    
    // Control de caja
    Route::get('/caja/estado', [\App\Http\Controllers\CashRegisterController::class, 'status'])->name('caja.estado');
    Route::post('/caja/abrir', [\App\Http\Controllers\CashRegisterController::class, 'open'])->name('caja.abrir');
    Route::post('/caja/cerrar', [\App\Http\Controllers\CashRegisterController::class, 'close'])->name('caja.cerrar');
});

// Panel (solo gerente)
Route::middleware(['auth', 'role:gerente'])->group(function () {
    Route::get('/panel', fn () => view('panel.index'))->name('panel.index');
});

// Vendedores (solo gerente)
Route::middleware(['auth', 'role:gerente'])->group(function () {
    Route::get('/panel/vendedores', [VendedorController::class, 'index'])->name('vendedores.index');
    Route::post('/panel/vendedores', [VendedorController::class, 'store'])->name('vendedores.store');
    Route::get('/panel/vendedores/{user}/edit', [VendedorController::class, 'edit'])->name('vendedores.edit');
    Route::put('/panel/vendedores/{user}', [VendedorController::class, 'update'])->name('vendedores.update');
    Route::delete('/panel/vendedores/{user}', [VendedorController::class, 'destroy'])->name('vendedores.destroy');
    Route::patch('/panel/vendedores/{user}/toggle', [VendedorController::class, 'toggle'])->name('vendedores.toggle');
});

// Módulos del gerente (operativos)
Route::middleware(['auth', 'role:gerente'])->group(function () {
    Route::get('/panel/reportes', [\App\Http\Controllers\DashboardStatsController::class, 'index'])->name('reportes.index');
    Route::get('/panel/reportes/pdf', [\App\Http\Controllers\DashboardStatsController::class, 'pdf'])->name('reportes.pdf');
    
    // IA del gerente
    Route::get('/panel/ia', [\App\Http\Controllers\AIController::class, 'index'])->name('ia.index');
    Route::post('/panel/ia', [\App\Http\Controllers\AIController::class, 'ask'])->name('panel.ia.ask');
    
    // Dashboard inteligente
    Route::get('/panel/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('panel.dashboard');
    Route::post('/panel/dashboard/ask', [\App\Http\Controllers\DashboardController::class, 'askQuick'])->name('panel.dashboard.ask');
    
    // Clima
    Route::get('/panel/clima', [\App\Http\Controllers\WeatherController::class, 'index'])->name('panel.clima');
    Route::get('/panel/clima-analisis', [\App\Http\Controllers\WeatherInsightController::class, 'index'])->name('panel.weather.insight');
    Route::post('/panel/clima-analisis/ask', [\App\Http\Controllers\WeatherInsightController::class, 'askClima'])->name('panel.weather.ask');
    
    // Configuración operativa
    Route::get('/panel/config', [\App\Http\Controllers\SettingsController::class, 'edit'])->name('panel.config');
    Route::post('/panel/config', [\App\Http\Controllers\SettingsController::class, 'update'])->name('panel.config.update');

        // Gráficas del reporte
        Route::get('/reportes/graficas', [DashboardStatsController::class, 'index'])
            ->name('reportes.graficas');

    // Reporte diario
    Route::get('/reporte-diario', [DailyReportController::class, 'show'])->name('reporte.diario');
    Route::get('/reporte-diario/pdf', [DailyReportController::class, 'pdf'])->name('reporte.diario.pdf');

    // Reporte por vendedores
    Route::get('/panel/reportes/vendedores', [\App\Http\Controllers\SellerReportController::class, 'index'])
        ->name('panel.reportes.vendedores');

    // Reporte semanal
    Route::get('/panel/reportes/semanal', [\App\Http\Controllers\WeeklyReportController::class, 'index'])
        ->name('panel.reportes.semanal');

    // Caja (monitoreo, cortes e historial)
    Route::get('/panel/caja', [\App\Http\Controllers\CashRegisterReportController::class, 'index'])
        ->name('panel.caja.index');
    Route::post('/panel/caja/registrar-dinero', [\App\Http\Controllers\CashRegisterController::class, 'registerRealAmount'])
        ->name('caja.registrar.dinero');

    // Bitácora de auditoría
    Route::get('/panel/bitacora', [\App\Http\Controllers\AuditLogController::class, 'index'])
        ->name('panel.bitacora');
});


// Módulo crítico solo admin
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/panel/config-critica', [\App\Http\Controllers\CriticalSettingsController::class, 'edit'])
        ->name('panel.config.critica');
    Route::post('/panel/config-critica', [\App\Http\Controllers\CriticalSettingsController::class, 'update'])
        ->name('panel.config.critica.update');
    Route::post('/panel/config-critica/gerente', [\App\Http\Controllers\CriticalSettingsController::class, 'updateGerente'])
        ->name('panel.config.critica.gerente');
    Route::post('/panel/config-critica/gerente/crear', [\App\Http\Controllers\CriticalSettingsController::class, 'storeGerente'])
        ->name('panel.config.critica.gerente.store');
    Route::post('/panel/config-critica/clear-cache', [\App\Http\Controllers\CriticalSettingsController::class, 'clearCache'])
        ->name('panel.config.critica.clear-cache');
    Route::post('/panel/config-critica/clean-logs', [\App\Http\Controllers\CriticalSettingsController::class, 'cleanOldLogs'])
        ->name('panel.config.critica.clean-logs');
    Route::post('/panel/config-critica/test-connections', [\App\Http\Controllers\CriticalSettingsController::class, 'testConnections'])
        ->name('panel.config.critica.test-connections');
    
    // Rutas de backups
    Route::post('/panel/config-critica/backup/create', [\App\Http\Controllers\CriticalSettingsController::class, 'createBackup'])
        ->name('panel.config.critica.backup.create');
    Route::get('/panel/config-critica/backup/download/{filename}', [\App\Http\Controllers\CriticalSettingsController::class, 'downloadBackup'])
        ->name('panel.config.critica.backup.download');
    Route::delete('/panel/config-critica/backup/{filename}', [\App\Http\Controllers\CriticalSettingsController::class, 'deleteBackup'])
        ->name('panel.config.critica.backup.delete');
});

Route::middleware(['auth', 'role:gerente'])->group(function () {

    // Productos (CRUD básico)
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
    Route::delete('/products/{product}/image', [ProductController::class, 'deleteImage'])->name('products.delete-image');

    // Entrada de mercancía (inventario)
    Route::post('/inventory/entry', [InventoryController::class, 'entry'])->name('inventory.entry');
});

// (Ya movido arriba)

// Temporal / accesos internos (si ya los usas)

Route::middleware('auth')->group(function () {
    Route::view('/sales', 'sales.index')->name('sales.index');
});


// PDF del ticket (solo gerente/admin)
Route::get('/ticket/{sale}/pdf', [\App\Http\Controllers\SaleController::class, 'pdf'])
    ->middleware(['auth', 'role:admin,gerente'])
    ->name('ticket.pdf');

// Ticket público para QR (debe ir al final)
Route::get('/ticket/{sale}', [\App\Http\Controllers\SaleController::class, 'show'])->name('ticket.show');
