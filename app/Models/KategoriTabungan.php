<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Tabungan;


class KategoriTabungan extends Model
{
    protected $table = 'tb_kategori_tabungan';

    protected $fillable = [
        'user_id',
        'nama_kategori',
        'dompet_tujuan_id',
        'target_nominal',
        'target_waktu',
    ];

    public function dompetTujuan()
    {
        return $this->belongsTo(Dompet::class, 'dompet_tujuan_id');
    }

    public function tabungan()
    {
        return $this->hasMany(Tabungan::class, 'kategori_tabungan_id');
    }

    public function catatTabungan()
{
    return $this->hasMany(Tabungan::class, 'kategori_tabungan_id');
}


    public function totalTerkumpul()
    {
        return $this->tabungan()->sum('nominal');
    }
}
