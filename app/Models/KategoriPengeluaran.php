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
    ];

    public function pengeluaran()
    {
        return $this->hasMany(Pengeluaran::class, 'kategori_id');
    }
}
