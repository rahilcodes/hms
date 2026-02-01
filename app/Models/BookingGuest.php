<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingGuest extends Model
{
    protected $fillable = [
        'booking_id',
        'name',
        'phone',
        'email',
        'nationality',
        'address',
        'purpose_of_visit',
        'id_proof_path',
        'signature_path',
        'preferences',
        'internal_notes',
        'tags',
    ];

    protected $casts = [
        'tags' => 'array',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
