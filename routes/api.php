<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Memanggil semua Model SIMTOKO Anda
use App\Models\Product; 
use App\Models\Inventory;
use App\Models\Sale;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Rute bawaan Laravel (Biarkan saja sebagai pelengkap)
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// 🚀 1. KATEGORI PRODUK
Route::get('/products', function () {
    try {
        $produk = Product::all();
        return response()->json([
            'success' => true,
            'message' => 'Lapor Jendral! Data produk berhasil ditarik.',
            'total_data' => $produk->count(),
            'data'    => $produk
        ], 200);
    } catch (\Throwable $e) {
        return response()->json(['success' => false, 'message' => 'Gagal menarik data: ' . $e->getMessage()], 500);
    }
});

// 🚀 2. KATEGORI INVENTORY (GUDANG)
Route::get('/inventory', function () {
    try {
        $inventory = Inventory::all();
        return response()->json([
            'success' => true,
            'message' => 'Lapor Jendral! Data inventori gudang berhasil ditarik.',
            'total_data' => $inventory->count(),
            'data'    => $inventory
        ], 200);
    } catch (\Throwable $e) {
        return response()->json(['success' => false, 'message' => 'Gagal menarik data: ' . $e->getMessage()], 500);
    }
});

// 🚀 3. KATEGORI SALES (PENJUALAN)
Route::get('/sales', function () {
    try {
        $sales = Sale::all();
        return response()->json([
            'success' => true,
            'message' => 'Lapor Jendral! Data riwayat penjualan berhasil ditarik.',
            'total_data' => $sales->count(),
            'data'    => $sales
        ], 200);
    } catch (\Throwable $e) {
        return response()->json(['success' => false, 'message' => 'Gagal menarik data: ' . $e->getMessage()], 500);
    }
});

// 🚀 4. KATEGORI USERS (PENGGUNA/KARYAWAN)
Route::get('/users', function () {
    try {
        $users = User::all();
        return response()->json([
            'success' => true,
            'message' => 'Lapor Jendral! Data pengguna/karyawan berhasil ditarik.',
            'total_data' => $users->count(),
            'data'    => $users
        ], 200);
    } catch (\Throwable $e) {
        return response()->json(['success' => false, 'message' => 'Gagal menarik data: ' . $e->getMessage()], 500);
    }
});