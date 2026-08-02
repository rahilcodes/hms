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
        'id_type',
        'id_number',
        'visa_number',
        'visa_expiry',
        'arrived_from',
        'next_destination',
    ];

    protected $casts = [
        'tags' => 'array',
        'visa_expiry' => 'date',
    ];

    public function isForeigner(): bool
    {
        $n = strtolower(trim((string) $this->nationality));
        return $n !== '' && !in_array($n, ['indian', 'india', 'in']);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
