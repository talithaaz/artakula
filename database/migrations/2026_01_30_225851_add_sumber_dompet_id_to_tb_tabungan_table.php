<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
{
    Schema::table('tb_tabungan', function (Blueprint $table) {
        $table->unsignedBigInteger('sumber_dompet_id')
            ->nullable()
            ->after('kategori_tabungan_id');
    });
}


public function down(): void
{
    Schema::table('tb_tabungan', function (Blueprint $table) {
        $table->dropColumn('sumber_dompet_id');
    });
}

};

