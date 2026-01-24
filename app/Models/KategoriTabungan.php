<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KategoriTabungan extends Model
{
    protected $table = 'tb_kategori_tabungan';

    protected $fillable = [
        'user_id',
        'nama_kategori',
        'target_nominal',
        'target_waktu',
    ];

    public function tabungan()
    {
        return $this->hasMany(Tabungan::class, 'kategori_tabungan_id');
    }

    public function totalTerkumpul()
    {
        return $this->tabungan()->sum('nominal');
    }
}
