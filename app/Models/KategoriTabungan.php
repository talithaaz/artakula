<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Tabungan;
use App\Models\Dompet;

class KategoriTabungan extends Model
{
    // Nama tabel di database
    protected $table = 'tb_kategori_tabungan';

    // Field yang boleh diisi mass assignment
    protected $fillable = [
        'user_id',
        'nama_kategori',
        'dompet_tujuan_id',
        'target_nominal',
        'target_waktu',
    ];

    /**
     * Relasi ke dompet tujuan
     */
    public function dompetTujuan()
    {
        return $this->belongsTo(Dompet::class, 'dompet_tujuan_id');
    }

    /**
     * Relasi ke tabel tabungan
     */
    public function tabungan()
    {
        return $this->hasMany(Tabungan::class, 'kategori_tabungan_id');
    }

    /**
     * Alias relasi tabungan
     * Digunakan di controller
     */
    public function catatTabungan()
    {
        return $this->hasMany(Tabungan::class, 'kategori_tabungan_id');
    }

    /**
     * Hitung total tabungan terkumpul
     */
    public function totalTerkumpul()
    {
        return $this->tabungan()->sum('nominal');
    }
}
