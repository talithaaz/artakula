<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::table('tb_kategori_tabungan', function (Blueprint $table) {
            if (!Schema::hasColumn('tb_kategori_tabungan', 'target_waktu')) {
                $table->date('target_waktu')
                      ->nullable()
                      ->after('target_nominal');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tb_kategori_tabungan', function (Blueprint $table) {
            $table->dropColumn('target_waktu');
        });
    }
};

