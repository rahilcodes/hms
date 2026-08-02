<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BanquetHall extends Model
{
    protected $fillable = [
        'hotel_id',
        'name',
        'capacity',
        'base_rent',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'base_rent' => 'decimal:2',
    ];

    public function bookings()
    {
        return $this->hasMany(BanquetBooking::class);
    }
}
