<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            // Tambahkan kolom fine_paid_at (waktu pembayaran denda)
            $table->timestamp('fine_paid_at')
                  ->nullable()
                  ->after('fine_status');
            
            // Tambahkan kolom fine_paid_by (siapa yang memvalidasi pembayaran)
            $table->unsignedBigInteger('fine_paid_by')
                  ->nullable()
                  ->after('fine_paid_at');
            
            // Optional: tambahkan foreign key ke tabel users
            // $table->foreign('fine_paid_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            // Hapus foreign key jika ada
            // $table->dropForeign(['fine_paid_by']);
            
            // Hapus kolom
            $table->dropColumn([
                'fine_paid_at',
                'fine_paid_by'
            ]);
        });
    }
};