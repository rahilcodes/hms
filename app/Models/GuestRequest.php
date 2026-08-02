<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GuestRequest extends Model
{
    protected $fillable = ['booking_id', 'type', 'request', 'status', 'staff_notes'];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
