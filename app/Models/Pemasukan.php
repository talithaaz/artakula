<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pemasukan extends Model
{
    protected $table = 'tb_pemasukan';

    protected $fillable = [
        'user_id',
        'dompet_id',
        'keterangan',
        'jumlah',
        'tanggal',
    ];

    public function dompet()
    {
        return $this->belongsTo(Dompet::class);
    }
}
