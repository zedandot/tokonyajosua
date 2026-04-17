<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Tabel supplier (pemasok barang).
     * Setiap penerimaan barang masuk (stock_receivings) dapat dikaitkan
     * ke supplier tertentu untuk keperluan laporan pembelian dan audit.
     */
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();

            $table->string('name', 150)->comment('Nama perusahaan / pemasok');
            $table->string('code', 30)->unique()->nullable()->comment('Kode supplier internal, misal: SUP-001');
            $table->string('contact_person', 100)->nullable()->comment('Nama PIC / kontak person');
            $table->string('phone', 25)->nullable()->comment('Nomor telepon supplier');
            $table->string('email', 150)->nullable()->comment('Email supplier');
            $table->text('address')->nullable()->comment('Alamat lengkap supplier');
            $table->boolean('is_active')->default(true)->comment('Status aktif supplier');
            $table->text('notes')->nullable()->comment('Catatan tambahan');
            $table->timestamps();
            $table->softDeletes();

            $table->index('name', 'idx_suppliers_name');
            $table->index('is_active', 'idx_suppliers_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
