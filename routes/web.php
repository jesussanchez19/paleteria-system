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
use App\Http\Controllers\WeatherController;

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
    if ($user->isGerente() || $user->isAdmin()) {
        return redirect()->route('panel.index');
    }
    return redirect('/');
})->middleware('auth')->name('redirect.after.login');


// Panel (solo gerente/admin)
Route::middleware(['auth', 'role:gerente,admin'])->group(function () {
    Route::get('/panel', fn () => view('panel.index'))->name('panel.index');

    // Vendedores
    Route::get('/panel/vendedores', [VendedorController::class, 'index'])->name('vendedores.index');
    Route::post('/panel/vendedores', [VendedorController::class, 'store'])->name('vendedores.store');
    Route::get('/panel/vendedores/{user}/edit', [VendedorController::class, 'edit'])->name('vendedores.edit');
    Route::put('/panel/vendedores/{user}', [VendedorController::class, 'update'])->name('vendedores.update');
    Route::delete('/panel/vendedores/{user}', [VendedorController::class, 'destroy'])->name('vendedores.destroy');
    Route::patch('/panel/vendedores/{user}/toggle', [VendedorController::class, 'toggle'])->name('vendedores.toggle');
});

// Módulos del gerente y admin
Route::middleware(['auth', 'role:admin,gerente'])->group(function () {
    Route::get('/panel/reportes', [\App\Http\Controllers\DashboardStatsController::class, 'index'])->name('reportes.index');
    Route::view('/panel/ia', 'panel.ia')->name('ia.index');
    Route::view('/panel/config', 'panel.config')->name('config.index');

        // Gráficas del reporte
        Route::get('/reportes/graficas', [DashboardStatsController::class, 'index'])
            ->middleware(['auth', 'role:admin,gerente'])
            ->name('reportes.graficas');

    // Reporte diario
    Route::get('/reporte-diario', [DailyReportController::class, 'show'])->name('reporte.diario');
    Route::get('/reporte-diario/pdf', [DailyReportController::class, 'pdf'])->name('reporte.diario.pdf');
});

//ruta de la api clima y recomendacion
Route::get('/analisis/{lat}/{lon}', [WeatherController::class, 'analyze']);


//crud de productos e inventario (solo gerente/admin)
Route::middleware(['auth', 'role:admin,gerente'])->group(function () {

    // Productos (CRUD básico)
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');

    // Entrada de mercancía (inventario)
    Route::post('/inventory/entry', [InventoryController::class, 'entry'])->name('inventory.entry');
});

// (Ya movido arriba)

// Temporal / accesos internos (si ya los usas)
Route::middleware('auth')->group(function () {
    Route::view('/sales', 'sales.index')->name('sales.index');
});


// Ticket público para QR (debe ir al final)
Route::get('/ticket/{sale}', [\App\Http\Controllers\SaleController::class, 'show'])->name('ticket.show');

//// FUNCIONES DE ADMINISTRACIÓN DE USUARIOS (solo admin)
// Módulo crítico solo admin
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::view('/panel/config-critica', 'panel.config-critica')->name('config.critical');
});

// POS (vendedor, gerente, admin)
Route::middleware(['auth', 'role:vendedor,gerente,admin'])->group(function () {
    Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
    Route::post('/pos/checkout', [SaleController::class, 'store'])->name('pos.checkout');
});

// PDF del ticket (solo gerente/admin)
Route::get('/ticket/{sale}/pdf', [\App\Http\Controllers\SaleController::class, 'pdf'])
    ->middleware(['auth', 'role:admin,gerente'])
    ->name('ticket.pdf');