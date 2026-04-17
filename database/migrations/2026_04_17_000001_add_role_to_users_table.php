<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Menambahkan kolom role, is_active, dan soft delete ke tabel users.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['owner', 'kasir', 'gudang'])
                  ->default('kasir')
                  ->after('email')
                  ->comment('Role pengguna dalam sistem');

            $table->boolean('is_active')
                  ->default(true)
                  ->after('role')
                  ->comment('Status aktif pengguna');

            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'is_active']);
            $table->dropSoftDeletes();
        });
    }
};
