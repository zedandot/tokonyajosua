<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabel master data produk.
     * Menyimpan informasi produk termasuk harga beli (modal) dan harga jual.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            $table->foreignId('category_id')
                  ->nullable()
                  ->constrained('categories')
                  ->nullOnDelete()
                  ->comment('FK ke categories; null jika kategori dihapus');

            $table->string('name', 200)->comment('Nama produk');
            $table->string('sku', 50)->unique()->comment('Stock Keeping Unit — kode unik produk');
            $table->text('description')->nullable()->comment('Deskripsi produk');

            $table->decimal('purchase_price', 15, 2)
                  ->default(0)
                  ->comment('Harga beli / harga modal (COGS)');

            $table->decimal('selling_price', 15, 2)
                  ->default(0)
                  ->comment('Harga jual ke pelanggan');

            $table->string('unit', 30)
                  ->default('pcs')
                  ->comment('Satuan: pcs, kg, ltr, box, lusin, dll');

            $table->string('image')->nullable()->comment('Path gambar produk');
            $table->boolean('is_active')->default(true)->comment('Produk aktif/nonaktif');
            $table->timestamps();
            $table->softDeletes();

            // Composite index untuk query filter umum
            $table->index(['category_id', 'is_active'], 'idx_products_category_active');
            $table->index('name', 'idx_products_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
