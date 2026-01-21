<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tb_pengeluaran', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('dompet_id');
            $table->unsignedBigInteger('kategori_id');
            $table->string('keterangan');
            $table->bigInteger('jumlah');
            $table->date('tanggal');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('dompet_id')->references('id')->on('tb_dompet')->onDelete('cascade');
            $table->foreign('kategori_id')->references('id')->on('tb_kategori_pengeluaran')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_pengeluaran');
    }
};
