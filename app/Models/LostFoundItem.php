<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LostFoundItem extends Model
{
    protected $fillable = [
        'hotel_id',
        'room_id',
        'found_by_user_id',
        'guest_id',
        'item_name',
        'description',
        'category',
        'found_location',
        'found_date',
        'status',
        'claimed_by_name',
        'claimed_date',
        'image_path',
        'notes',
    ];

    protected $casts = [
        'found_date' => 'datetime',
        'claimed_date' => 'datetime',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function foundBy()
    {
        return $this->belongsTo(Admin::class, 'found_by_user_id');
    }

    public function guest()
    {
        return $this->belongsTo(BookingGuest::class, 'guest_id');
    }
}
