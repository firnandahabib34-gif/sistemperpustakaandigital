<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->enum('extend_status', ['menunggu', 'disetujui', 'ditolak'])->nullable()->after('extended_count');
            $table->timestamp('extend_requested_at')->nullable()->after('extend_status');
        });
    }

    public function down()
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->dropColumn(['extend_status', 'extend_requested_at']);
        });
    }
};