<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    // 1. Menampilkan Halaman POS (Menarik data asli dari database)
    public function index()
    {
        // Hanya ambil produk yang aktif dan punya stok
        $products = Product::with('inventory')
            ->where('is_active', true)
            ->whereHas('inventory', function($query) {
                $query->where('current_stock', '>', 0);
            })
            ->get();

        return view('sales.index', compact('products'));
    }

    // 2. Memproses Pembayaran dari JavaScript
    public function store(Request $request)
    {
        // Validasi data yang dikirim dari JS
        $request->validate([
            'total' => 'required|numeric',
            'items' => 'required|array',
            'items.*.id' => 'required|exists:products,id',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric',
        ]);

        // Gunakan Database Transaction agar jika ada error di tengah jalan, 
        // uang dan stok tidak jadi terpotong (aman dari error setengah jalan).
        try {
            DB::beginTransaction();

           // A. Buat Induk Transaksi (Nota)
            $sale = Sale::create([
                'user_id' => auth()->id(), // Kasir yang bertugas
                'invoice_number' => 'INV-' . date('YmdHis') . '-' . rand(10, 99), // <-- TAMBAHAN BARU: Generate otomatis
                'total_amount' => $request->total,
                'status' => 'completed'
            ]);

            // B. Proses setiap barang di keranjang
            foreach ($request->items as $item) {
                // Catat di detail nota
                SaleItem::create([
                    'sale_id'    => $sale->id,
                    'product_id' => $item['id'],
                    'quantity'   => $item['qty'],
                    'unit_price' => $item['price'],
                    'subtotal'   => $item['qty'] * $item['price'] 
                ]);

                // Tarik data produk dan kurangi stoknya
                $product = Product::with('inventory')->lockForUpdate()->find($item['id']);
                
                if ($product->inventory) {
                    $stockBefore = $product->inventory->current_stock;
                    $stockAfter = $stockBefore - $item['qty'];

                    // Update sisa stok
                    $product->inventory->update(['current_stock' => $stockAfter]);

                    // C. Catat di Buku Pergerakan Gudang
                    StockMovement::create([
                        'product_id' => $product->id,
                        'type' => 'out', // Barang keluar
                        'quantity' => -$item['qty'], // Minus karena keluar
                        'stock_before' => $stockBefore,
                        'stock_after' => $stockAfter,
                        'notes' => 'Sale #' . $sale->id,
                    ]);
                }
            }

            DB::commit();

            // Balas ke JavaScript bahwa pembayaran sukses
            return response()->json(['success' => true, 'message' => 'Transaksi berhasil dicatat!']);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()], 500);
        }
    }
}