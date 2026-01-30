<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::table('tb_tabungan', function (Blueprint $table) {
        $table->foreign('sumber_dompet_id')
            ->references('id')
            ->on('tb_dompet')
            ->cascadeOnDelete();
    });
}

public function down(): void
{
    Schema::table('tb_tabungan', function (Blueprint $table) {
        $table->dropForeign(['sumber_dompet_id']);
    });
}

};
