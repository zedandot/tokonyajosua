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

    // 🟢 FUNGSI BARU: Menyimpan keranjang ke session sementara
    public function saveSession(Request $request)
    {
        session(['pos_cart' => $request->items, 'pos_total' => $request->total]);
        return response()->json(['redirect' => route('sales.payment')]);
    }

    // 🟢 FUNGSI BARU: Menampilkan halaman form pembayaran
    public function payment()
    {
        // Cegah akses jika keranjang kosong
        if (!session()->has('pos_cart') || empty(session('pos_cart'))) {
            return redirect()->route('sales.index')->with('error', 'Keranjang belanja kosong.');
        }

        $cart = session('pos_cart');
        $total = session('pos_total');

        return view('sales.payment', compact('cart', 'total'));
    }

    // 2. Memproses Pembayaran ke Database
    public function store(Request $request)
    {
        // Validasi data yang dikirim dari JS
        $request->validate([
            'total' => 'required|numeric',
            'items' => 'required|array',
            'items.*.id' => 'required|exists:products,id',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric',
            'payment_method' => 'nullable|string', // 🟢 Tambahan Validasi Metode
        ]);

        // Gunakan Database Transaction
        try {
            DB::beginTransaction();

            // 🟢 Tangkap metode pembayaran (Default: TUNAI)
            $method = strtoupper($request->payment_method ?? 'TUNAI');

           // A. Buat Induk Transaksi (Nota)
            $sale = Sale::create([
                'user_id' => auth()->id(), // Kasir yang bertugas
                'invoice_number' => 'INV-' . date('YmdHis') . '-' . rand(10, 99),
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
                        'notes' => 'Sale #' . $sale->id . ' via ' . $method, // 🟢 Rekam metode pembayaran
                    ]);
                }
            }

            DB::commit();

            // Bersihkan session keranjang setelah berhasil
            session()->forget(['pos_cart', 'pos_total']);

            // Balas ke JavaScript bahwa pembayaran sukses
            return response()->json(['success' => true, 'message' => 'Transaksi berhasil dicatat!']);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()], 500);
        }
    }
}