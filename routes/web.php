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

// Público
Route::get('/', fn () => redirect()->route('catalogo.index'));

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
    
    // Clima
    Route::get('/panel/clima', [\App\Http\Controllers\WeatherController::class, 'index'])->name('panel.clima');
    Route::get('/panel/clima-analisis', [\App\Http\Controllers\WeatherInsightController::class, 'index'])->name('panel.weather.insight');
    
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
    Route::post('/panel/config-critica/clear-cache', [\App\Http\Controllers\CriticalSettingsController::class, 'clearCache'])
        ->name('panel.config.critica.clear-cache');
    Route::post('/panel/config-critica/clean-logs', [\App\Http\Controllers\CriticalSettingsController::class, 'cleanOldLogs'])
        ->name('panel.config.critica.clean-logs');
    Route::post('/panel/config-critica/test-connections', [\App\Http\Controllers\CriticalSettingsController::class, 'testConnections'])
        ->name('panel.config.critica.test-connections');
});

Route::middleware(['auth', 'role:gerente'])->group(function () {

    // Productos (CRUD básico)
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

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
