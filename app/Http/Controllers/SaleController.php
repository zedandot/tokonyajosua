<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Category; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    // 1. Menampilkan Halaman POS (Menarik data asli dari database)
    public function index()
    {
        // 🟢 PERBAIKAN BACKEND: 
        // Menggunakan teknik Collection Filter agar lebih stabil di MongoDB
        // dan tidak memblokir produk baru yang status aktifnya masih kosong (null).
        
        $allProducts = Product::with(['inventory', 'category'])->latest()->get();

        $products = $allProducts->filter(function ($product) {
            // 1. Anggap produk aktif selama statusnya tidak secara tegas diset "false"
            $isActive = $product->is_active !== false; 
            
            // 2. Ambil stok dari gudang (inventory)
            $stock = $product->inventory->current_stock ?? 0;
            
            // Tampilkan di kasir JIKA aktif DAN stok lebih dari 0
            return $isActive && $stock > 0;
        })->values(); // Reset urutan data

        $categories = Category::all();

        return view('sales.index', compact('products', 'categories'));
    }

    // Menyimpan keranjang ke session sementara
    public function saveSession(Request $request)
    {
        session(['pos_cart' => $request->items, 'pos_total' => $request->total]);
        return response()->json(['redirect' => route('sales.payment')]);
    }

    // Menampilkan halaman form pembayaran
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
            'payment_method' => 'nullable|string', 
        ]);

        // Gunakan Database Transaction
        try {
            DB::beginTransaction();

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
                        'type' => 'out', 
                        'quantity' => -$item['qty'], 
                        'stock_before' => $stockBefore,
                        'stock_after' => $stockAfter,
                        'notes' => 'Sale #' . $sale->id . ' via ' . $method, 
                    ]);
                }
            }

            DB::commit();

            // Bersihkan session keranjang setelah berhasil
            session()->forget(['pos_cart', 'pos_total']);

            return response()->json(['success' => true, 'message' => 'Transaksi berhasil dicatat!']);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()], 500);
        }
    }
}