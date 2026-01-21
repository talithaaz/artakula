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
    Schema::create('tb_pemasukan', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained('tb_users')->onDelete('cascade');
        $table->foreignId('dompet_id')->constrained('tb_dompet')->cascadeOnDelete();
        $table->string('keterangan');
        $table->integer('jumlah');
        $table->date('tanggal');
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_pemasukan');
    }
};
