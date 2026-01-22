<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('tb_kategori_pengeluaran', function (Blueprint $table) {
            $table->date('periode_awal')->nullable()->after('budget');
            $table->date('periode_akhir')->nullable()->after('periode_awal');
        });
    }

    public function down()
    {
        Schema::table('tb_kategori_pengeluaran', function (Blueprint $table) {
            $table->dropColumn(['periode_awal', 'periode_akhir']);
        });
    }
};
