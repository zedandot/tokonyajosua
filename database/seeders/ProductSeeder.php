<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\StockMovement;
use App\Models\User;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Bersihkan sisa data lama agar gudang rapi
        Product::truncate();
        Category::truncate();
        Inventory::truncate();
        StockMovement::truncate();

        // Cari petugas gudang atau owner untuk dicatat sebagai penanggung jawab stok awal
        $petugas = User::where('role', 'gudang')->first() ?? User::first();

        // 2. Daftar Pasokan Barang Rumah Tangga
        $products = [
            // ─── ELEKTRONIK ──────────────────────────────────────────
            ['category' => 'Elektronik', 'name' => 'TV LED 32 Inch', 'sku' => 'ELK-001', 'purchase' => 1800000, 'sell' => 2500000, 'unit' => 'unit', 'stock' => 15, 'min' => 5],
            ['category' => 'Elektronik', 'name' => 'Kipas Angin Berdiri', 'sku' => 'ELK-002', 'purchase' => 150000, 'sell' => 250000, 'unit' => 'unit', 'stock' => 30, 'min' => 10],
            ['category' => 'Elektronik', 'name' => 'Rice Cooker 1.8L', 'sku' => 'ELK-003', 'purchase' => 200000, 'sell' => 350000, 'unit' => 'unit', 'stock' => 20, 'min' => 5],

            // ─── FURNITUR ────────────────────────────────────────────
            ['category' => 'Furnitur', 'name' => 'Sofa Minimalis 3 Seater', 'sku' => 'FURN-001', 'purchase' => 2500000, 'sell' => 3200000, 'unit' => 'set', 'stock' => 8, 'min' => 2],
            ['category' => 'Furnitur', 'name' => 'Meja Makan Kayu Jati', 'sku' => 'FURN-002', 'purchase' => 1200000, 'sell' => 1800000, 'unit' => 'set', 'stock' => 5, 'min' => 2],
            ['category' => 'Furnitur', 'name' => 'Rak Sepatu 4 Susun', 'sku' => 'FURN-003', 'purchase' => 100000, 'sell' => 175000, 'unit' => 'pcs', 'stock' => 12, 'min' => 3],

            // ─── PECAH BELAH ─────────────────────────────────────────
            // (Sengaja dibuat stok rendah untuk memicu notifikasi peringatan di dashboard)
            ['category' => 'Pecah Belah', 'name' => 'Set Piring Kaca Keramik (6 Pcs)', 'sku' => 'PCB-001', 'purchase' => 100000, 'sell' => 150000, 'unit' => 'set', 'stock' => 3, 'min' => 10],
            ['category' => 'Pecah Belah', 'name' => 'Gelas Kopi Beling Tebal', 'sku' => 'PCB-002', 'purchase' => 8000, 'sell' => 15000, 'unit' => 'pcs', 'stock' => 50, 'min' => 20],
            ['category' => 'Pecah Belah', 'name' => 'Mangkuk Sup Porselen', 'sku' => 'PCB-003', 'purchase' => 20000, 'sell' => 35000, 'unit' => 'pcs', 'stock' => 8, 'min' => 15],

            // ─── PERABOTAN ───────────────────────────────────────────
            ['category' => 'Perabotan', 'name' => 'Sapu Lidi Tebal', 'sku' => 'PRB-001', 'purchase' => 15000, 'sell' => 25000, 'unit' => 'pcs', 'stock' => 45, 'min' => 10],
            ['category' => 'Perabotan', 'name' => 'Alat Pel Microfiber', 'sku' => 'PRB-002', 'purchase' => 45000, 'sell' => 75000, 'unit' => 'set', 'stock' => 25, 'min' => 5],
            ['category' => 'Perabotan', 'name' => 'Ember Plastik 20L + Tutup', 'sku' => 'PRB-003', 'purchase' => 30000, 'sell' => 55000, 'unit' => 'pcs', 'stock' => 30, 'min' => 8],
        ];

        foreach ($products as $data) {
            // A. Buat Kategori (Otomatis tidak duplikat)
            $category = Category::firstOrCreate(['name' => $data['category']]);

            // B. Buat Produk
            $product = Product::create([
                'category_id'    => $category->id,
                'name'           => $data['name'],
                'sku'            => $data['sku'],
                'purchase_price' => $data['purchase'],
                'selling_price'  => $data['sell'],
                'unit'           => $data['unit'],
                'is_active'      => true,
            ]);

            // C. Buat Inventori
            Inventory::create([
                'product_id'    => $product->id,
                'current_stock' => $data['stock'],
                'minimum_stock' => $data['min'],
            ]);

            // D. Catat Riwayat Masuk (Stock Movement) agar tabel riwayat terisi
            if ($petugas) {
                StockMovement::create([
                    'product_id'   => $product->id,
                    'user_id'      => $petugas->id,
                    'type'         => 'in',
                    'quantity'     => $data['stock'],
                    'stock_before' => 0,
                    'stock_after'  => $data['stock'],
                    'notes'        => 'Pasokan awal gudang (Dummy Data)',
                ]);
            }
        }
    }
}