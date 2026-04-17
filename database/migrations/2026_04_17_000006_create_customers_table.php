<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabel pelanggan — opsional, mendukung fitur kasir di masa depan.
     * Transaksi tanpa pelanggan tetap valid (customer_id nullable di sales).
     */
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150)->comment('Nama pelanggan');
            $table->string('phone', 20)->nullable()->unique()->comment('Nomor HP (unik)');
            $table->string('email', 150)->nullable()->unique()->comment('Email pelanggan (unik)');
            $table->text('address')->nullable()->comment('Alamat pengiriman/domisili');
            $table->timestamps();
            $table->softDeletes();

            $table->index('name', 'idx_customers_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
