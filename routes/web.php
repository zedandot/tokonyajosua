<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

Route::get('/', fn () => redirect()->route('login'));

Route::get('/login', fn () => view('auth.login'))->name('login');
Route::post('/login', function (\Illuminate\Http\Request $request) {
    $request->validate(['role' => 'required|in:owner,cashier,warehouse']);
    Session::put('role', $request->role);
    return redirect()->route('dashboard');
});

Route::get('/dashboard', function () {
    $role = Session::get('role', 'owner');
    return match ($role) {
        'owner' => view('dashboard.index'),
        'cashier' => view('dashboard.cashier'),
        'warehouse' => view('dashboard.warehouse'),
        default => redirect()->route('login'),
    };
})->name('dashboard');
Route::get('/sales', fn () => view('sales.index'))->name('sales.index');
Route::get('/products', fn () => view('products.index'))->name('products.index');
Route::get('/inventory', fn () => view('inventory.index'))->name('inventory.index');
Route::get('/reports', fn () => view('reports.index'))->name('reports.index');
Route::get('/users', fn () => view('users.index'))->name('users.index');
