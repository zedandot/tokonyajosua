<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    // Menampilkan halaman utama Inventory (Fungsi yang tadi hilang)
    public function index()
    {
        // Ambil produk beserta relasi inventory-nya
        $products = Product::with('inventory')->get();
        
        // Ambil 20 riwayat pergerakan stok terbaru
        $movements = StockMovement::with('product')->latest()->limit(20)->get();

        return view('inventory.index', compact('products', 'movements'));
    }

    // Menampilkan halaman form barang masuk (Record Incoming)
    public function create()
    {
        $products = Product::where('is_active', true)->get();
        return view('inventory.create', compact('products'));
    }

    // Memproses penambahan stok dengan rekam jejak lengkap
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1',
            'notes'      => 'nullable|string|max:255',
        ]);

        // Ambil data produk beserta data inventarisnya
        $product = Product::with('inventory')->findOrFail($request->product_id);

        // Hitung stok sebelum dan sesudah
        $stockBefore = $product->inventory ? $product->inventory->current_stock : 0;
        $stockAfter  = $stockBefore + $request->quantity;

        // 1. Update jumlah stok di tabel Inventory
        if ($product->inventory) {
            $product->inventory->update(['current_stock' => $stockAfter]);
        } else {
            // Jaga-jaga jika produk belum punya data inventory sama sekali
            $product->inventory()->create([
                'current_stock' => $stockAfter,
                'minimum_stock' => 0
            ]);
        }

        // 2. Catat riwayat rekam jejak lengkap di StockMovement
        StockMovement::create([
            'product_id'   => $product->id,
            'type'         => 'in',
            'quantity'     => $request->quantity,
            'stock_before' => $stockBefore, // Laporan stok sebelum
            'stock_after'  => $stockAfter,  // Laporan stok sesudah
            'notes'        => $request->notes ?? 'Restock',
        ]);

        return redirect()->route('inventory.index')->with('success', 'Stok barang berhasil ditambahkan!');
    }
}