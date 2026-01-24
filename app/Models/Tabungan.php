<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tabungan extends Model
{
    protected $table = 'tb_tabungan';

    protected $fillable = [
        'user_id',
        'kategori_tabungan_id',
        'dompet_id',
        'tanggal',
        'nominal',
        'catatan',
    ];

    public function kategori()
    {
        return $this->belongsTo(
            KategoriTabungan::class,
            'kategori_tabungan_id',
            'id'
        );
    }

    public function dompet()
    {
        return $this->belongsTo(Dompet::class, 'dompet_id');
    }
}
