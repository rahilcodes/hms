<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TravelAgent extends Model
{
    protected $fillable = [
        'hotel_id',
        'name',
        'agency_name',
        'phone',
        'email',
        'gst_number',
        'commission_percent',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'commission_percent' => 'decimal:2',
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'agent_id');
    }
}
