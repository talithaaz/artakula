<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('tb_wallets', 'tb_dompet');
    }

    public function down(): void
    {
        Schema::rename('tb_dompet', 'tb_wallets');
    }
};

