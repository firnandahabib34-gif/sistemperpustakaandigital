<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->timestamp('extended_at')->nullable()->after('anggota_confirmed');
            $table->integer('extended_count')->default(0)->after('extended_at');
        });
    }

    public function down()
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->dropColumn(['extended_at', 'extended_count']);
        });
    }
};