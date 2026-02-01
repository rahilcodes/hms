<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlockedDate extends Model
{
    protected $fillable = [
        'room_type_id',
        'date',
        'blocked_rooms',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function roomType()
    {
        return $this->belongsTo(RoomType::class);
    }
}
