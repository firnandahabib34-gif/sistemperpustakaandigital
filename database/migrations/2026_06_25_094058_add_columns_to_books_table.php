<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('books', function (Blueprint $table) {
            $table->string('isbn', 20)->nullable()->after('id');
            $table->string('lokasi_rak', 50)->nullable()->after('tahun');
            $table->text('deskripsi')->nullable()->after('lokasi_rak');
            $table->integer('jumlah_halaman')->nullable()->after('deskripsi');
            $table->string('sampul')->nullable()->after('jumlah_halaman');
        });
    }

    public function down()
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn(['isbn', 'lokasi_rak', 'deskripsi', 'jumlah_halaman', 'sampul']);
        });
    }
};