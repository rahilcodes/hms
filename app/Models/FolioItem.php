<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FolioItem extends Model
{
    use \App\Traits\BelongsToHotel;

    protected $fillable = [
        'hotel_id',
        'booking_id',
        'type',
        'description',
        'amount',
        'reference_date',
        'posted_at',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'reference_date' => 'date',
        'posted_at' => 'datetime',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
