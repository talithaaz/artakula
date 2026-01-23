<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KategoriTabungan extends Model
{
    protected $table = 'tb_kategori_tabungan';

    protected $fillable = [
        'user_id',
        'nama_tabungan',
        'keterangan',
        'target_nominal',
        'target_mulai',
        'target_selesai'
    ];

    public function tabungan()
    {
        return $this->hasMany(Tabungan::class, 'kategori_tabungan_id');
    }
}
