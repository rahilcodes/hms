<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use \App\Traits\BelongsToHotel;
    protected $fillable = [
        'booking_id',
        'hotel_id',
        'provider',
        'provider_payment_id',
        'amount',
        'status',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
