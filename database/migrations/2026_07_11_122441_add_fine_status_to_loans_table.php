<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loans', function (Blueprint $table) {

            $table->enum('fine_status', [
                'belum_bayar',
                'sudah_bayar'
            ])->default('belum_bayar')->after('fine');

        });
    }

    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {

            $table->dropColumn('fine_status');

        });
    }
};