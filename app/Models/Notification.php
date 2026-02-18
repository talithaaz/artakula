<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table = 'tb_notifikasi';

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'reference',
        'is_read'
    ];
}
