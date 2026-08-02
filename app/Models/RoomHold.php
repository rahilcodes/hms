<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomHold extends Model
{
    protected $fillable = [
        'session_id',
        'room_type_id',
        'check_in',
        'check_out',
        'rooms',
        'expires_at',
    ];

    protected $casts = [
        'check_in' => 'date',
        'check_out' => 'date',
        'expires_at' => 'datetime',
    ];
}
