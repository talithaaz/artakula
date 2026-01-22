<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KategoriPengeluaran extends Model
{
    protected $table = 'tb_kategori_pengeluaran';

    protected $fillable = [
        'user_id',
        'nama_kategori',
        'budget',
        'periode_awal',    // ditambahkan
        'periode_akhir',   // ditambahkan
    ];

    public function pengeluaran()
    {
        return $this->hasMany(Pengeluaran::class, 'kategori_id');
    }
}
