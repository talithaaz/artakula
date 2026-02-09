<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tb_evaluasi', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id');

            $table->integer('bulan');
            $table->integer('tahun');

            // Ringkasan Keuangan
            $table->bigInteger('total_pemasukan')->default(0);
            $table->bigInteger('total_pengeluaran')->default(0);
            $table->bigInteger('total_tabungan')->default(0);
            $table->bigInteger('sisa_kas')->default(0);
            $table->decimal('rasio_tabungan',5,2)->default(0);

            // Hasil Analisis Sistem
            $table->string('predikat')->nullable();
            $table->string('trend_keuangan')->nullable();
            $table->string('kategori_dominan')->nullable();
            $table->decimal('persen_dominan',5,2)->default(0);

            // Smart Recalculate
            $table->timestamp('last_calculated_at')->nullable();

            $table->timestamps();

            $table->unique(['user_id','bulan','tahun']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_evaluasi');
    }
};
