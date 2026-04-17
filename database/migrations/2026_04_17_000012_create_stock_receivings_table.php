<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Tabel penerimaan barang (stock receiving / purchase receiving).
     * Ini adalah DOKUMEN SUMBER resmi untuk setiap barang yang masuk ke gudang.
     *
     * Mengapa butuh tabel ini selain stock_movements?
     * - stock_movements = log atomik per-produk (satu row per produk)
     * - stock_receivings = dokumen header (satu pengiriman bisa berisi banyak produk)
     * - Mirip seperti hubungan sales ↔ sale_items, ini adalah receiving ↔ receiving_items
     *
     * Status alur:
     *   draft → confirmed → received → cancelled
     *
     * Catatan: status 'received' adalah satu-satunya yang mempengaruhi stok.
     */
    public function up(): void
    {
        Schema::create('stock_receivings', function (Blueprint $table) {
            $table->id();

            $table->string('receiving_number', 30)
                  ->unique()
                  ->comment('Nomor penerimaan — format: RCV-YYYYMMDD-XXXX');

            $table->foreignId('supplier_id')
                  ->nullable()
                  ->constrained('suppliers')
                  ->nullOnDelete()
                  ->comment('Supplier asal barang (opsional — bisa penerimaan internal)');

            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete()
                  ->comment('Petugas gudang yang menerima');

            $table->enum('status', ['draft', 'confirmed', 'received', 'cancelled'])
                  ->default('draft')
                  ->comment('draft=belum final, confirmed=disetujui, received=stok sudah diupdate, cancelled=batal');

            $table->string('reference_document', 100)
                  ->nullable()
                  ->comment('Nomor PO / surat jalan / dokumen referensi eksternal');

            $table->date('received_date')
                  ->nullable()
                  ->comment('Tanggal fisik barang diterima di gudang');

            $table->decimal('total_cost', 15, 2)
                  ->default(0)
                  ->comment('Total biaya pembelian (harga modal keseluruhan)');

            $table->text('notes')
                  ->nullable()
                  ->comment('Catatan penerimaan (kondisi barang, kekurangan, dll)');

            $table->timestamps();
            $table->softDeletes();

            // Index untuk query laporan barang masuk
            $table->index(['status', 'received_date'], 'idx_receiving_status_date');
            $table->index('received_date', 'idx_receiving_date');
            $table->index('supplier_id', 'idx_receiving_supplier');
            $table->index('user_id', 'idx_receiving_user');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_receivings');
    }
};
