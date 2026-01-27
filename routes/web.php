<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\VendedorController;

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
    Route::delete('/panel/vendedores/{user}', [VendedorController::class, 'destroy'])->name('vendedores.destroy');
    Route::patch('/panel/vendedores/{user}/toggle', [VendedorController::class, 'toggle'])->name('vendedores.toggle');

    // Secciones (vistas)
    Route::view('/panel/reportes', 'panel.reportes')->name('reportes.index');
    Route::view('/panel/ia', 'panel.ia')->name('ia.index');
});

// Config (solo admin)
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::view('/panel/config', 'panel.config')->name('config.index');
});

// Temporal / accesos internos (si ya los usas)
Route::middleware('auth')->group(function () {
    Route::view('/products', 'products.index')->name('products.index');
    Route::view('/sales', 'sales.index')->name('sales.index');
});
