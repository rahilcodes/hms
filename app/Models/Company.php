<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'gst_number',
        'address',
        'credit_limit',
        'is_active'
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
