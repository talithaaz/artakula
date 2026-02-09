<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evaluasi extends Model
{
    protected $table = 'tb_evaluasi';

    protected $fillable = [
        'user_id',
        'bulan',
        'tahun',
        'total_pemasukan',
        'total_pengeluaran',
        'total_tabungan',
        'sisa_kas',
        'rasio_tabungan',
        'predikat',
        'trend_keuangan',
        'kategori_dominan',
        'persen_dominan',
        'last_calculated_at'
    ];
}

