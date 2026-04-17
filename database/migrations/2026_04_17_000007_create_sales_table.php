<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabel header transaksi penjualan.
     * Setiap penjualan memiliki invoice_number unik dan menyimpan
     * total finansial (subtotal, diskon, pajak, total, kembalian).
     */
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();

            $table->string('invoice_number', 30)
                  ->unique()
                  ->comment('Format: INV-YYYYMMDD-XXXX, dibuat otomatis');

            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete()
                  ->comment('Kasir yang memproses transaksi');

            $table->foreignId('customer_id')
                  ->nullable()
                  ->constrained('customers')
                  ->nullOnDelete()
                  ->comment('Pelanggan (opsional — bisa transaksi umum/tamu)');

            $table->enum('status', ['pending', 'completed', 'cancelled', 'refunded'])
                  ->default('completed')
                  ->comment('Status transaksi');

            $table->decimal('subtotal', 15, 2)
                  ->default(0)
                  ->comment('Total sebelum diskon dan pajak');

            $table->decimal('discount_amount', 15, 2)
                  ->default(0)
                  ->comment('Diskon keseluruhan transaksi');

            $table->decimal('tax_amount', 15, 2)
                  ->default(0)
                  ->comment('Pajak (PPN, atau lainnya)');

            $table->decimal('total_amount', 15, 2)
                  ->default(0)
                  ->comment('Total yang harus dibayar: subtotal - discount + tax');

            $table->decimal('amount_paid', 15, 2)
                  ->default(0)
                  ->comment('Uang tunai yang diberikan pelanggan');

            $table->decimal('change_amount', 15, 2)
                  ->default(0)
                  ->comment('Kembalian: amount_paid - total_amount');

            $table->text('notes')->nullable()->comment('Catatan transaksi');

            $table->timestamp('sold_at')
                  ->useCurrent()
                  ->comment('Waktu transaksi — bisa berbeda dari created_at jika diinput manual');

            $table->timestamps();
            $table->softDeletes();

            // Index untuk query laporan & dashboard
            $table->index(['status', 'sold_at'], 'idx_sales_status_date');
            $table->index('sold_at', 'idx_sales_sold_at');
            $table->index('user_id', 'idx_sales_user');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
