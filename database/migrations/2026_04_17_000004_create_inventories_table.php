<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabel inventori — menyimpan stok real-time per produk.
     * Relasi 1:1 dengan products (satu produk = satu record inventori).
     */
    public function up(): void
    {
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')
                  ->unique()   // Memastikan relasi 1:1
                  ->constrained('products')
                  ->cascadeOnDelete()
                  ->comment('FK ke products; hapus produk = hapus inventori');

            $table->integer('current_stock')
                  ->default(0)
                  ->comment('Jumlah stok saat ini (real-time)');

            $table->integer('minimum_stock')
                  ->default(5)
                  ->comment('Batas minimum stok — trigger notifikasi jika <= nilai ini');

            $table->integer('maximum_stock')
                  ->nullable()
                  ->comment('Kapasitas maksimum gudang (opsional)');

            $table->timestamps();

            // Index untuk query stok menipis di dashboard
            $table->index('current_stock', 'idx_inventories_stock');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};
