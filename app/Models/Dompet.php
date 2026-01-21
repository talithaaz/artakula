<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dompet extends Model
{
    use HasFactory;

    protected $table = 'tb_dompet';

    protected $fillable = [
        'user_id',
        'nama_dompet',
        'jenis',
        'bank_code',
        'saldo',
        'is_dummy',
        'last_sync_at',
    ];

    protected $casts = [
        'last_sync_at' => 'datetime',
        'is_dummy' => 'boolean',
        'saldo' => 'integer',
    ];
}
