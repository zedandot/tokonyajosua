<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Tabel item-item dalam keranjang belanja kasir.
     * Harga disimpan sebagai snapshot dari harga jual produk saat item
     * ditambahkan — agar total cart tidak berubah jika harga produk diupdate
     * di tengah transaksi.
     *
     * Constraint UNIQUE (cart_id, product_id) mencegah produk duplikat
     * dalam cart yang sama — kasir cukup update quantity jika produk sama.
     */
    public function up(): void
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('cart_id')
                  ->constrained('carts')
                  ->cascadeOnDelete()
                  ->comment('FK ke cart — hapus cart = hapus semua item-nya');

            $table->foreignId('product_id')
                  ->constrained('products')
                  ->cascadeOnDelete()
                  ->comment('FK ke produk yang ditambahkan');

            $table->integer('quantity')
                  ->default(1)
                  ->comment('Jumlah unit dalam keranjang');

            $table->decimal('unit_price', 15, 2)
                  ->comment('Snapshot harga jual saat item ditambahkan ke cart');

            $table->decimal('discount_amount', 15, 2)
                  ->default(0)
                  ->comment('Diskon per item (jika ada)');

            $table->decimal('subtotal', 15, 2)
                  ->storedAs('(unit_price - discount_amount) * quantity')
                  ->comment('Computed column: (unit_price - discount_amount) * quantity');

            $table->timestamps();

            // Mencegah produk duplikat dalam cart yang sama
            $table->unique(['cart_id', 'product_id'], 'uq_cart_product');

            $table->index('cart_id', 'idx_cart_items_cart');
            $table->index('product_id', 'idx_cart_items_product');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
