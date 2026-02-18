<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tb_notifikasi', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');

            // jenis kondisi
            $table->string('type'); 
            // contoh: budget, saldo, evaluasi, tabungan

            $table->string('title');
            $table->text('message');

            // biar notif tidak dobel terus
            $table->string('reference')->nullable();
            // contoh: kategori_3_bulan_2

            $table->boolean('is_read')->default(false);

            $table->timestamps();

            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_notifikasi');
    }
};
