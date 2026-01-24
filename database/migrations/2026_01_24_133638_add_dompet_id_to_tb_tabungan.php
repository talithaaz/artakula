<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('tb_tabungan', function (Blueprint $table) {
        $table->unsignedBigInteger('dompet_id')->after('kategori_tabungan_id');

        $table->foreign('dompet_id')
              ->references('id')
              ->on('tb_dompet')
              ->onDelete('cascade');
    });
}

public function down()
{
    Schema::table('tb_tabungan', function (Blueprint $table) {
        $table->dropForeign(['dompet_id']);
        $table->dropColumn('dompet_id');
    });
}
};
