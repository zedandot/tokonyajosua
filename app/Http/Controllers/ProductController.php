<?php

namespace App\Http\Controllers;

use App\Models\Product; 
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category')->latest();

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where('name', 'like', '%' . $searchTerm . '%');
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        $products = $query->get();
        $categories = Category::all();
        
        return view('products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        // 🚀 VALIDASI DINAMIS: Harga tidak wajib jika user adalah Kasir
        $rules = [
            'name'           => 'required|string|max:255',
            'category_id'    => 'required|exists:categories,id',
            'stock'          => 'required|integer',
            'min_stock'      => 'required|integer',
        ];

        if (auth()->user()->role === 'owner') {
            $rules['purchase_price'] = 'required|numeric';
            $rules['selling_price']  = 'required|numeric';
        }

        $request->validate($rules);

        // 🚀 LOGIKA GENERATOR SKU CERDAS
        $kategori = Category::find($request->category_id);
        $namaKategori = strtolower($kategori->name ?? 'lainnya'); 
        
        $prefix = 'PRD';
        if (str_contains($namaKategori, 'elektronik')) {
            $prefix = 'ELK';
        } elseif (str_contains($namaKategori, 'furniture') || str_contains($namaKategori, 'mebel')) {
            $prefix = 'FURN';
        } elseif (str_contains($namaKategori, 'pecah belah')) {
            $prefix = 'PCB';
        } elseif (str_contains($namaKategori, 'perabotan')) {
            $prefix = 'PRB';
        }

        $lastProduct = Product::where('sku', 'like', $prefix . '-%')->orderBy('sku', 'desc')->first();
        $nextSequence = $lastProduct ? (int) str_replace($prefix . '-', '', $lastProduct->sku) + 1 : 1;
        $skuBaru = $prefix . '-' . str_pad($nextSequence, 3, '0', STR_PAD_LEFT);

        // 🚀 PENENTUAN HARGA BERDASARKAN ROLE
        $purchasePrice = auth()->user()->role === 'owner' ? $request->purchase_price : 0;
        $sellingPrice = auth()->user()->role === 'owner' ? $request->selling_price : 0;

        $product = Product::create([
            'name'           => $request->name,
            'category_id'    => $request->category_id,
            'purchase_price' => $purchasePrice,
            'selling_price'  => $sellingPrice,
            'sku'            => $skuBaru,
            'is_active'      => true,
        ]);

        $product->inventory()->create([
            'current_stock' => $request->stock,
            'minimum_stock' => $request->min_stock,
        ]);

        return redirect()->route('products.index')->with('success', 'Product ' . $skuBaru . ' berhasil ditambahkan!');
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        return view('products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        // Validasi Update
        $rules = [
            'name'           => 'required|string|max:255',
            'category_id'    => 'required|exists:categories,id',
            'stock'          => 'required|integer',
            'min_stock'      => 'required|integer',
        ];

        if (auth()->user()->role === 'owner') {
            $rules['purchase_price'] = 'required|numeric';
            $rules['selling_price']  = 'required|numeric';
        }

        $request->validate($rules);

        // Update data dasar
        $updateData = [
            'name'        => $request->name,
            'category_id' => $request->category_id,
        ];

        // Jika owner yang update, update juga harganya
        if (auth()->user()->role === 'owner') {
            $updateData['purchase_price'] = $request->purchase_price;
            $updateData['selling_price']  = $request->selling_price;
        }

        $product->update($updateData);

        $product->inventory()->updateOrCreate(
            ['product_id' => $product->id],
            [
                'current_stock' => $request->stock,
                'minimum_stock' => $request->min_stock,
            ]
        );

        return redirect()->route('products.index')->with('success', 'Data product berhasil diperbarui!');
    }

    public function destroy(Product $product)
    {
        // 🚀 PROTEKSI HAPUS: Hanya Owner yang boleh hapus
        if (auth()->user()->role !== 'owner') {
            return back()->with('error', 'Akses ditolak! Hanya Owner yang dapat menghapus produk.');
        }
        
        $product->delete();
        return back()->with('success', 'Product berhasil dihapus!');
    }
}