<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;
use App\Models\Inventory;

class ProductSeeder extends Seeder
{
    /**
     * Seed produk contoh beserta inventori masing-masing.
     * Satu produk Air Mineral sengaja diset stok rendah untuk
     * menguji fitur notifikasi stok menipis di dashboard.
     */
    public function run(): void
    {
        $products = [
            // Elektronik
            [
                'category'       => 'Elektronik',
                'name'           => 'Lampu LED 10W',
                'sku'            => 'ELK-001',
                'purchase_price' => 15000,
                'selling_price'  => 25000,
                'unit'           => 'pcs',
                'stock'          => 50,
                'min_stock'      => 10,
            ],
            [
                'category'       => 'Elektronik',
                'name'           => 'Baterai AA (isi 4)',
                'sku'            => 'ELK-002',
                'purchase_price' => 10000,
                'selling_price'  => 16000,
                'unit'           => 'pak',
                'stock'          => 30,
                'min_stock'      => 5,
            ],
            // Makanan
            [
                'category'       => 'Makanan',
                'name'           => 'Mie Instan Goreng',
                'sku'            => 'MKN-001',
                'purchase_price' => 3000,
                'selling_price'  => 4500,
                'unit'           => 'pcs',
                'stock'          => 200,
                'min_stock'      => 30,
            ],
            [
                'category'       => 'Makanan',
                'name'           => 'Biskuit Crackers 200g',
                'sku'            => 'MKN-002',
                'purchase_price' => 8000,
                'selling_price'  => 12000,
                'unit'           => 'pcs',
                'stock'          => 80,
                'min_stock'      => 15,
            ],
            // Minuman — stok rendah (uji notifikasi)
            [
                'category'       => 'Minuman',
                'name'           => 'Air Mineral 600ml',
                'sku'            => 'MNM-001',
                'purchase_price' => 2000,
                'selling_price'  => 3500,
                'unit'           => 'botol',
                'stock'          => 3,   // Di bawah minimum_stock=20 → notifikasi muncul
                'min_stock'      => 20,
            ],
            [
                'category'       => 'Minuman',
                'name'           => 'Teh Kotak 250ml',
                'sku'            => 'MNM-002',
                'purchase_price' => 3500,
                'selling_price'  => 5000,
                'unit'           => 'pcs',
                'stock'          => 60,
                'min_stock'      => 10,
            ],
            // Perawatan Diri
            [
                'category'       => 'Perawatan Diri',
                'name'           => 'Sabun Mandi 80g',
                'sku'            => 'PRD-001',
                'purchase_price' => 4000,
                'selling_price'  => 6500,
                'unit'           => 'pcs',
                'stock'          => 40,
                'min_stock'      => 8,
            ],
        ];

        foreach ($products as $data) {
            $category = Category::where('name', $data['category'])->first();

            $product = Product::updateOrCreate(
                ['sku' => $data['sku']],
                [
                    'category_id'    => $category?->id,
                    'name'           => $data['name'],
                    'sku'            => $data['sku'],
                    'purchase_price' => $data['purchase_price'],
                    'selling_price'  => $data['selling_price'],
                    'unit'           => $data['unit'],
                    'is_active'      => true,
                ]
            );

            // Buat atau update inventori untuk produk ini
            Inventory::updateOrCreate(
                ['product_id' => $product->id],
                [
                    'current_stock' => $data['stock'],
                    'minimum_stock' => $data['min_stock'],
                ]
            );
        }
    }
}
