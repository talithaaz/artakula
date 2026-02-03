<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KategoriPengeluaran extends Model
{
    // Nama tabel di database
    protected $table = 'tb_kategori_pengeluaran';

    // Field yang boleh diisi secara mass assignment
    protected $fillable = [
        'user_id',
        'nama_kategori',
        'budget',
        'periode_awal',
        'periode_akhir',
    ];

    /**
     * Relasi one-to-many
     * Satu kategori memiliki banyak pengeluaran
     */
    public function pengeluaran()
    {
        return $this->hasMany(Pengeluaran::class, 'kategori_id');
    }
}
