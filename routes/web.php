<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\VendedorController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\InventoryController;

// Público
Route::get('/', fn () => redirect()->route('catalogo.index'));

Route::get('/catalogo', [CatalogController::class, 'index'])
    ->name('catalogo.index');

// Auth (Breeze)
require __DIR__.'/auth.php';

// Básico autenticado (Breeze)
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', fn () => view('dashboard'))
        ->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// POS (vendedor, gerente, admin)
Route::middleware(['auth', 'role:vendedor,gerente,admin'])->group(function () {
    Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
    Route::post('/pos/checkout', [SaleController::class, 'store'])->name('pos.checkout');
});

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
    Route::view('/panel/reportes', 'panel.reportes')->name('reportes.index');
    Route::view('/panel/ia', 'panel.ia')->name('ia.index');
    Route::view('/panel/config', 'panel.config')->name('config.index');
});

// Módulo crítico solo admin
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::view('/panel/config-critica', 'panel.config-critica')->name('config.critical');
});

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


// PDF del ticket (solo gerente/admin)
Route::get('/ticket/{sale}/pdf', [\App\Http\Controllers\SaleController::class, 'pdf'])
    ->middleware(['auth', 'role:admin,gerente'])
    ->name('ticket.pdf');

// Ticket público para QR (debe ir al final)
Route::get('/ticket/{sale}', [\App\Http\Controllers\SaleController::class, 'show'])->name('ticket.show');
