<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tabungan extends Model
{
    use HasFactory;

    // Nama tabel
    protected $table = 'tb_tabungan';

    // Kolom yang boleh diisi (mass assignment)
    protected $fillable = [
        'user_id',
        'kategori_tabungan_id',
        'sumber_dompet_id',
        'dompet_id',
        'tanggal',
        'nominal',
        'keterangan',
    ];

    /**
     * Relasi ke kategori tabungan
     */
    public function kategori()
    {
        return $this->belongsTo(KategoriTabungan::class, 'kategori_tabungan_id');
    }

    /**
     * Relasi ke dompet tujuan
     */
    public function dompet()
    {
        return $this->belongsTo(Dompet::class, 'dompet_id');
    }

    /**
     * Relasi ke dompet sumber
     */
    public function sumberDompet()
    {
        return $this->belongsTo(Dompet::class, 'sumber_dompet_id');
    }

    /**
     * Relasi ke user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
