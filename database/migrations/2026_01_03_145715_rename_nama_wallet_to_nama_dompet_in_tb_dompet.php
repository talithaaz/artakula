<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tb_dompet', function (Blueprint $table) {
            $table->renameColumn('nama_wallet', 'nama_dompet');
        });
    }

    public function down(): void
    {
        Schema::table('tb_dompet', function (Blueprint $table) {
            $table->renameColumn('nama_dompet', 'nama_wallet');
        });
    }
};
