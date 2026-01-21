<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengeluaran extends Model
{
    protected $table = 'tb_pengeluaran';

    protected $fillable = [
        'user_id',
        'dompet_id',
        'kategori_id',
        'keterangan',
        'jumlah',
        'tanggal',
    ];

    public function dompet()
    {
        return $this->belongsTo(Dompet::class);
    }

    public function kategori()
    {
        return $this->belongsTo(KategoriPengeluaran::class, 'kategori_id');
    }
}
