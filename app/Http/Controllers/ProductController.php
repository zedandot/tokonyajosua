<?php

namespace App\Http\Controllers;

use App\Models\Product; // Tetap konsisten menggunakan Product
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        // 1. Mulai merakit kueri MongoDB
        $query = Product::with('category')->latest();

        // 2. Jika ada parameter pencarian (search)
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            // Mencari berdasarkan nama produk
            $query->where('name', 'like', '%' . $searchTerm . '%');
        }

        // 3. Jika ada parameter filter kategori
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // 4. Eksekusi kueri akhir
        $products = $query->get();
        
        $categories = Category::all();
        
        return view('products.index', compact('products', 'categories'));
    }

    // Menampilkan halaman form tambah produk
    public function create()
    {
        $categories = Category::all();
        return view('products.create', compact('categories'));
    }

    // Menyimpan data ke Product & Inventory
    public function store(Request $request)
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'category_id'    => 'required|exists:categories,id',
            'purchase_price' => 'required|numeric',
            'selling_price'  => 'required|numeric',
            'stock'          => 'required|integer',
            'min_stock'      => 'required|integer',
        ]);

        // 1. Simpan Data Product & Generate SKU Otomatis
        $product = Product::create([
            'name'           => $request->name,
            'category_id'    => $request->category_id,
            'purchase_price' => $request->purchase_price,
            'selling_price'  => $request->selling_price,
            'sku'            => 'PRD-' . time() . '-' . rand(100, 999), // Otomatis dibuat
            'is_active'      => true, // Default aktif
        ]);

        // 2. Simpan Data Stok ke Tabel Inventory secara terpisah
        $product->inventory()->create([
            'current_stock' => $request->stock,
            'minimum_stock' => $request->min_stock,
        ]);

        return redirect()->route('products.index')->with('success', 'Product berhasil ditambahkan!');
    }

    // Menampilkan halaman form edit produk
    public function edit(Product $product)
    {
        $categories = Category::all();
        return view('products.edit', compact('product', 'categories'));
    }

    // Memperbarui data di Product & Inventory
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'category_id'    => 'required|exists:categories,id',
            'purchase_price' => 'required|numeric',
            'selling_price'  => 'required|numeric',
            'stock'          => 'required|integer',
            'min_stock'      => 'required|integer',
        ]);

        // 1. Update Data Product
        $product->update([
            'name'           => $request->name,
            'category_id'    => $request->category_id,
            'purchase_price' => $request->purchase_price,
            'selling_price'  => $request->selling_price,
        ]);

        // 2. Update Data Stok di Tabel Inventory
        $product->inventory()->updateOrCreate(
            ['product_id' => $product->id],
            [
                'current_stock' => $request->stock,
                'minimum_stock' => $request->min_stock,
            ]
        );

        return redirect()->route('products.index')->with('success', 'Data product berhasil diperbarui!');
    }

    // Menghapus data produk
    public function destroy(Product $product)
    {
        $product->delete();
        return back()->with('success', 'Product berhasil dihapus!');
    }
}