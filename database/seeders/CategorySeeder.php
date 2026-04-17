<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Seed kategori produk awal.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Elektronik',       'description' => 'Produk elektronik dan kelistrikan'],
            ['name' => 'Makanan',           'description' => 'Produk makanan dan snack'],
            ['name' => 'Minuman',           'description' => 'Produk minuman dan air'],
            ['name' => 'Perawatan Diri',    'description' => 'Produk kebersihan dan perawatan tubuh'],
            ['name' => 'Alat Tulis',        'description' => 'Peralatan tulis dan kantor'],
            ['name' => 'Rumah Tangga',      'description' => 'Peralatan dan kebutuhan rumah tangga'],
        ];

        foreach ($categories as $data) {
            Category::updateOrCreate(
                ['slug' => Str::slug($data['name'])],
                [
                    'name'        => $data['name'],
                    'slug'        => Str::slug($data['name']),
                    'description' => $data['description'],
                ]
            );
        }
    }
}
