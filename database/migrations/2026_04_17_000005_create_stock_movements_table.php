<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabel riwayat pergerakan stok (audit trail).
     * Setiap transaksi yang mengubah stok (penjualan, pembelian, koreksi)
     * dicatat di sini untuk keperluan laporan dan audit.
     */
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')
                  ->constrained('products')
                  ->cascadeOnDelete()
                  ->comment('Produk yang bergerak');

            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete()
                  ->comment('User yang mencatat (kasir/gudang/owner)');

            // Polymorphic-style: bisa merujuk ke sales, purchase_orders, manual_adjustments, dll.
            $table->unsignedBigInteger('reference_id')
                  ->nullable()
                  ->comment('ID dokumen sumber (misal: ID penjualan)');

            $table->string('reference_type', 50)
                  ->nullable()
                  ->comment('Tipe dokumen: sale | purchase | adjustment');

            $table->enum('type', ['in', 'out', 'adjustment'])
                  ->comment('in=masuk, out=keluar(penjualan), adjustment=koreksi manual');

            $table->integer('quantity')
                  ->comment('Jumlah unit yang bergerak (selalu positif)');

            $table->integer('stock_before')
                  ->comment('Stok sebelum pergerakan ini terjadi');

            $table->integer('stock_after')
                  ->comment('Stok sesudah pergerakan ini terjadi');

            $table->text('notes')
                  ->nullable()
                  ->comment('Keterangan tambahan / alasan koreksi');

            $table->timestamps();

            // Index untuk query laporan & filter riwayat
            $table->index(['product_id', 'type'], 'idx_stock_mov_product_type');
            $table->index(['reference_type', 'reference_id'], 'idx_stock_mov_reference');
            $table->index('created_at', 'idx_stock_mov_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
