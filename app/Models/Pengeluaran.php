<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengeluaran extends Model
{
    // Nama tabel
    protected $table = 'tb_pengeluaran';

    // Field yang boleh diisi mass assignment
    protected $fillable = [
        'user_id',
        'dompet_id',
        'kategori_id',
        'keterangan',
        'jumlah',
        'tanggal',
    ];

    // Relasi ke tabel dompet
    public function dompet()
    {
        return $this->belongsTo(Dompet::class);
    }

    // Relasi ke tabel kategori pengeluaran
    public function kategori()
    {
        return $this->belongsTo(KategoriPengeluaran::class, 'kategori_id');
    }
}
