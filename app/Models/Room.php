<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_type_id',
        'room_number',
        'floor',
        'status',
        'housekeeping_status',
        'notes',
    ];

    public function roomType()
    {
        return $this->belongsTo(RoomType::class);
    }

    public function assets()
    {
        return $this->hasMany(Asset::class);
    }

    public function bookings()
    {
        return $this->belongsToMany(Booking::class, 'booking_room')->withTimestamps();
    }
}
