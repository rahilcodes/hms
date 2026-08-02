<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BanquetBooking extends Model
{
    protected $fillable = [
        'hotel_id',
        'banquet_hall_id',
        'company_id',
        'customer_name',
        'customer_phone',
        'customer_email',
        'customer_gstin',
        'event_type',
        'event_date',
        'start_time',
        'end_time',
        'guests_expected',
        'per_plate_rate',
        'food_plates',
        'hall_rent',
        'decoration_charge',
        'other_charges',
        'discount',
        'advance_paid',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'event_date' => 'date',
        'per_plate_rate' => 'decimal:2',
        'hall_rent' => 'decimal:2',
        'decoration_charge' => 'decimal:2',
        'other_charges' => 'decimal:2',
        'discount' => 'decimal:2',
        'advance_paid' => 'decimal:2',
    ];

    public const EVENT_TYPES = ['wedding', 'reception', 'conference', 'birthday', 'other'];
    public const STATUSES = ['enquiry', 'confirmed', 'completed', 'cancelled'];

    public function hall()
    {
        return $this->belongsTo(BanquetHall::class, 'banquet_hall_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function getFoodTotalAttribute(): float
    {
        return (float) $this->per_plate_rate * (int) $this->food_plates;
    }

    public function getTotalAmountAttribute(): float
    {
        return $this->food_total
            + (float) $this->hall_rent
            + (float) $this->decoration_charge
            + (float) $this->other_charges
            - (float) $this->discount;
    }

    public function getBalanceAmountAttribute(): float
    {
        return $this->total_amount - (float) $this->advance_paid;
    }
}
