<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabel detail item per transaksi penjualan.
     * Harga modal (purchase_price) disimpan sebagai snapshot saat transaksi
     * sehingga laporan profit tetap akurat meskipun harga modal berubah kemudian.
     */
    public function up(): void
    {
        Schema::create('sale_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('sale_id')
                  ->constrained('sales')
                  ->cascadeOnDelete()
                  ->comment('Jika transaksi dihapus, detail item ikut terhapus');

            $table->foreignId('product_id')
                  ->constrained('products')
                  ->restrictOnDelete()
                  ->comment('Produk tidak dapat dihapus selama masih ada di transaksi');

            $table->integer('quantity')
                  ->comment('Jumlah unit yang terjual');

            $table->decimal('unit_price', 15, 2)
                  ->comment('Harga jual per unit SAAT transaksi (snapshot — tidak berubah meski harga produk diubah)');

            $table->decimal('purchase_price', 15, 2)
                  ->default(0)
                  ->comment('Harga modal per unit SAAT transaksi (snapshot — untuk kalkulasi profit akurat)');

            $table->decimal('discount_amount', 15, 2)
                  ->default(0)
                  ->comment('Diskon per item (bukan diskon keseluruhan)');

            $table->decimal('subtotal', 15, 2)
                  ->comment('(unit_price - discount_amount) * quantity');

            $table->timestamps();

            // Index untuk query laporan & join
            $table->index('sale_id', 'idx_sale_items_sale');
            $table->index('product_id', 'idx_sale_items_product');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_items');
    }
};
