<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomServiceOrder extends Model
{
    protected $fillable = ['booking_id', 'items', 'total_amount', 'status', 'notes'];

    protected $casts = [
        'items' => 'array',
        'total_amount' => 'decimal:2',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
