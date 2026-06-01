<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SaleController; 
use App\Http\Controllers\UserController;
use App\Http\Middleware\CheckRole;  

// ─── Halaman Utama ─────────────────────────────────────────────
Route::get('/', fn () => redirect()->route('login'));

// ─── Auth (Tamu saja, belum login) ─────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// ─── Logout  ───────────────────────────────────────────────────
Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// ─── Semua Route Butuh Login ────────────────────────────────────
Route::middleware('auth')->group(function () {

    // Dashboard — redirect sesuai role
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Owner: semua fitur
    Route::middleware(CheckRole::class.':owner')->group(function () {
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

        // Rute untuk mengelola user
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        // 🟢 TAMBAHAN: Rute untuk memberhentikan (hapus) user
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });

    // Owner + Kasir: POS / Sales
    Route::middleware(CheckRole::class.':owner,kasir')->group(function () {
        Route::post('/sales/checkout-session', [SaleController::class, 'saveSession'])->name('sales.checkout_session');
        Route::get('/sales/payment', [SaleController::class, 'payment'])->name('sales.payment');
        Route::get('/sales', [SaleController::class, 'index'])->name('sales.index');
        Route::post('/sales', [SaleController::class, 'store'])->name('sales.store');
    });

    // Owner + Kasir + Gudang: Products
    Route::middleware(CheckRole::class.':owner,kasir,gudang')->group(function () {
        Route::get('/products', [ProductController::class, 'index'])->name('products.index');
        Route::get('/products/create', [ProductController::class, 'create'])->name('products.create'); 
        Route::post('/products', [ProductController::class, 'store'])->name('products.store');
        Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit'); 
        Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
    });

    // Owner + Gudang: Inventory
    Route::middleware(CheckRole::class.':owner,gudang')->group(function () {
        Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
        Route::get('/inventory/create', [InventoryController::class, 'create'])->name('inventory.create');
        Route::post('/inventory', [InventoryController::class, 'store'])->name('inventory.store');
    });
});