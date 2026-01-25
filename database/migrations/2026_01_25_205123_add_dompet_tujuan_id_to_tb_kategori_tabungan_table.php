<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tb_kategori_tabungan', function (Blueprint $table) {
            $table->unsignedBigInteger('dompet_tujuan_id')
                ->nullable()
                ->after('user_id');

            $table->foreign('dompet_tujuan_id')
                ->references('id')
                ->on('tb_dompet')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('tb_kategori_tabungan', function (Blueprint $table) {
            $table->dropForeign(['dompet_tujuan_id']);
            $table->dropColumn('dompet_tujuan_id');
        });
    }
};
