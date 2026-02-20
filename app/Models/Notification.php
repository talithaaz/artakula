<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table = 'tb_notifikasi';

    protected $primaryKey = 'id';      // <- PENTING
    public $incrementing = true;       // <- PENTING
    protected $keyType = 'int';        // <- PENTING

    public $timestamps = true;         // <- SANGAT PENTING

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'reference',
        'is_read'
    ];
}