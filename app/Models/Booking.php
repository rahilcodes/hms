<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use SoftDeletes;

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string)\Illuminate\Support\Str::uuid();
            }
        });
    }

    protected $fillable = [
        'hotel_id',
        'uuid',
        'group_id',
        'company_id',
        'room_type_id',
        'check_in',
        'check_out',
        'rooms',
        'total_amount',
        'status',
        'checked_in_at',
        'checked_out_at',
        'rechecked_by',
        'services_json',
        'meta',
        'expires_at',
        'coupon_id',
        'discount_amount',
        'booking_type',
        'day_use_hours',
        'agent_id',
        'agent_commission',
    ];

    protected $casts = [
        'meta' => 'array',
        'check_in' => 'date',
        'check_out' => 'date',
        'checked_in_at' => 'datetime',
        'checked_out_at' => 'datetime',
        'services_json' => 'array',
        'discount_amount' => 'decimal:2',
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function roomType()
    {
        return $this->belongsTo(RoomType::class);
    }

    public function agent()
    {
        return $this->belongsTo(TravelAgent::class, 'agent_id');
    }

    public function isDayUse(): bool
    {
        return $this->booking_type === 'day_use';
    }

    public function guests()
    {
        return $this->hasMany(\App\Models\BookingGuest::class);
    }

    public function assignedRooms()
    {
        return $this->belongsToMany(Room::class , 'booking_room')->withTimestamps();
    }

    public function folioItems()
    {
        return $this->hasMany(FolioItem::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function guestRequests()
    {
        return $this->hasMany(GuestRequest::class);
    }

    public function roomServiceOrders()
    {
        return $this->hasMany(RoomServiceOrder::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    /**
     * Normalize rooms attribute to always be an array.
     * Handles legacy data where rooms was stored as an integer count.
     */
    public function getRoomsAttribute($value)
    {
        if (is_array($value)) {
            return $value;
        }

        if (is_numeric($value)) {
            return [$this->room_type_id => (int)$value];
        }

        return [];
    }

    /**
     * Get the room quantity for a specific type or total count.
     */
    public function roomCount($typeId = null)
    {
        $rooms = $this->rooms;

        if (is_array($rooms)) {
            if ($typeId) {
                return $rooms[$typeId] ?? 0;
            }
            return array_sum($rooms);
        }

        return (int)$rooms;
    }

    public function isCorporate()
    {
        return !empty($this->company_id);
    }

    public function getOperationalStatusAttribute()
    {
        $now = now();

        if ($this->status === 'cancelled')
            return 'cancelled';

        $checkIn = \Carbon\Carbon::parse($this->check_in);
        $checkOut = \Carbon\Carbon::parse($this->check_out);

        if (!$this->checked_in_at) {
            if ($checkIn->isToday() && $now->hour >= 14) { // Assume 2 PM check-in
                return 'pending_checkin';
            }
            if ($checkIn->isPast()) {
                return 'no_show';
            }
            return 'upcoming';
        }

        if (!$this->checked_out_at) {
            if ($checkOut->isToday() && $now->hour >= 11) { // Assume 11 AM check-out
                return 'pending_checkout';
            }
            if ($checkOut->isPast()) {
                return 'overdue_checkout';
            }
            return 'in_house';
        }

        return 'checked_out';
    }

    public function getPaidAmountAttribute()
    {
        $advance = $this->meta['advance_paid'] ?? 0;
        $settlements = $this->meta['payments'] ?? [];

        $settledAmount = collect($settlements)->sum('amount');

        // Gateway payments (Razorpay etc.) recorded against this booking
        $gatewayAmount = \App\Models\Payment::where('booking_id', $this->id)
            ->where('status', 'paid')
            ->sum('amount');

        // Backward compatibility for legacy flag
        if ($this->meta['paid_at_hotel'] ?? false) {
            return $this->total_amount;
        }

        return $advance + $settledAmount + $gatewayAmount;
    }

    public function getBalanceAmountAttribute()
    {
        return max(0, $this->total_bill - $this->paid_amount);
    }

    public function getTotalBillAttribute()
    {
        $diningTotal = $this->relationLoaded('roomServiceOrders')
            ? $this->roomServiceOrders->where('status', '!=', 'cancelled')->sum('total_amount')
            : $this->roomServiceOrders()->where('status', '!=', 'cancelled')->sum('total_amount');

        return $this->total_amount + $diningTotal;
    }

    public function getNightsAttribute()
    {
        return \Carbon\Carbon::parse($this->check_in)->diffInDays(\Carbon\Carbon::parse($this->check_out)) ?: 1;
    }

    public function activityLogs()
    {
        return $this->morphMany(ActivityLog::class , 'target');
    }
}
