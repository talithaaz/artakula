<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tabungan extends Model
{
    use HasFactory;

    protected $table = 'tb_tabungan';

    protected $fillable = [
        'user_id',
        'kategori_tabungan_id',
        'sumber_dompet_id',
        'dompet_id',
        'tanggal',
        'nominal',
        'keterangan',
    ];

    public function kategori()
    {
        return $this->belongsTo(KategoriTabungan::class, 'kategori_tabungan_id');
    }

    public function dompet()
    {
        return $this->belongsTo(Dompet::class, 'dompet_id');
    }

    public function sumberDompet()
{
    return $this->belongsTo(Dompet::class, 'sumber_dompet_id');
}


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
