<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('loans', function (Blueprint $table) {
            // Ubah ke string dengan constraint
            $table->string('status', 20)
                  ->default('menunggu')
                  ->change();
        });
        
        // Tambahkan constraint jika perlu
        DB::statement("ALTER TABLE loans ADD CONSTRAINT check_status CHECK (status IN ('menunggu', 'dipinjam', 'menunggu_validasi', 'dikembalikan', 'ditolak'))");
    }

    public function down()
    {
        DB::statement("ALTER TABLE loans DROP CONSTRAINT check_status");
        
        Schema::table('loans', function (Blueprint $table) {
            $table->string('status', 20)
                  ->default('menunggu')
                  ->change();
        });
    }
};