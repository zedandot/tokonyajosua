<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Tabel detail item per dokumen penerimaan barang.
     * Setiap row = satu produk dalam satu pengiriman.
     *
     * unit_cost disimpan sebagai snapshot harga beli saat penerimaan —
     * berguna untuk laporan COGS (Cost of Goods Sold) yang akurat.
     *
     * Kolom quantity_received vs quantity_ordered memungkinkan pencatatan
     * penerimaan parsial (misal: pesan 100 unit, terima 80 unit dulu).
     */
    public function up(): void
    {
        Schema::create('stock_receiving_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('stock_receiving_id')
                  ->constrained('stock_receivings')
                  ->cascadeOnDelete()
                  ->comment('FK ke dokumen penerimaan induk');

            $table->foreignId('product_id')
                  ->constrained('products')
                  ->restrictOnDelete()
                  ->comment('Produk yang diterima');

            $table->integer('quantity_ordered')
                  ->default(0)
                  ->comment('Jumlah yang dipesan / direncanakan (bisa 0 jika langsung terima)');

            $table->integer('quantity_received')
                  ->comment('Jumlah unit yang benar-benar diterima di gudang');

            $table->decimal('unit_cost', 15, 2)
                  ->default(0)
                  ->comment('Harga beli per unit (snapshot harga modal saat penerimaan)');

            $table->decimal('subtotal', 15, 2)
                  ->storedAs('unit_cost * quantity_received')
                  ->comment('Computed: unit_cost * quantity_received');

            $table->text('notes')
                  ->nullable()
                  ->comment('Catatan per item (kondisi barang, kerusakan, dll)');

            $table->timestamps();

            // Mencegah produk duplikat dalam satu dokumen penerimaan
            $table->unique(['stock_receiving_id', 'product_id'], 'uq_receiving_product');

            $table->index('stock_receiving_id', 'idx_recv_items_receiving');
            $table->index('product_id', 'idx_recv_items_product');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_receiving_items');
    }
};
