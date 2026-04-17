<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Tabel cart (keranjang belanja) — menyimpan sesi belanja kasir
     * yang belum di-checkout. Setiap kasir hanya bisa punya 1 cart aktif.
     *
     * Kenapa disimpan di DB (bukan Session)?
     * - Tahan refresh/restart server
     * - Bisa diaudit jika terjadi masalah
     * - Mendukung multi-tab / multi-device kasir yang sama
     * - Cart yang gagal checkout bisa direcovery
     */
    public function up(): void
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                  ->unique()                   // 1 kasir = 1 cart aktif
                  ->constrained('users')
                  ->cascadeOnDelete()
                  ->comment('Kasir pemilik cart — unique: 1 kasir hanya boleh punya 1 cart aktif');

            $table->foreignId('customer_id')
                  ->nullable()
                  ->constrained('customers')
                  ->nullOnDelete()
                  ->comment('Pelanggan yang dipilih (opsional)');

            $table->decimal('discount_amount', 15, 2)
                  ->default(0)
                  ->comment('Diskon keseluruhan yang diinput kasir');

            $table->text('notes')
                  ->nullable()
                  ->comment('Catatan transaksi (opsional)');

            $table->timestamps();

            $table->index('user_id', 'idx_carts_user');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
