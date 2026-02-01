<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use SoftDeletes;

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
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) \Illuminate\Support\Str::uuid();
            }
        });
    }

    protected $casts = [
        'meta' => 'array',
        'check_in' => 'date',
        'check_out' => 'date',
        'checked_in_at' => 'datetime',
        'checked_out_at' => 'datetime',
        'services_json' => 'array',
        'discount_amount' => 'decimal:2',
    ];

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

        // Backward compatibility for legacy flag
        if ($this->meta['paid_at_hotel'] ?? false) {
            return $this->total_amount;
        }

        return $advance + $settledAmount;
    }

    public function getBalanceAmountAttribute()
    {
        return max(0, $this->total_bill - $this->paid_amount);
    }

    public function getTotalBillAttribute()
    {
        $diningTotal = $this->roomServiceOrders()->where('status', '!=', 'cancelled')->sum('total_amount');
        return $this->total_amount + $diningTotal;
    }

    public function getNightsAttribute()
    {
        return \Carbon\Carbon::parse($this->check_in)->diffInDays(\Carbon\Carbon::parse($this->check_out)) ?: 1;
    }

    public function getDepositRequirementAttribute()
    {
        $settings = \App\Models\SiteSetting::all()->pluck('value', 'key');

        // Check if advanced payment features are active
        if (($settings['payment_feature_enabled'] ?? '0') === '0') {
            return 0;
        }

        $mode = $settings['payment_mode'] ?? 'hotel_only';

        if ($mode === 'hotel_only') {
            return 0;
        }

        if ($mode === 'online_only') {
            return $this->total_amount;
        }

        // Partial Deposit
        $type = $settings['deposit_type'] ?? 'percentage';
        $val = (float) ($settings['deposit_value'] ?? 100);

        if ($type === 'percentage') {
            return round(($this->total_amount * $val) / 100, 2);
        }

        return min($val, $this->total_amount);
    }

    /**
     * Normalize rooms attribute to always be an array.
     * Handles legacy data where rooms was stored as an integer count.
     */
    public function getRoomsAttribute($value)
    {
        // If the cast already made it an array, return it.
        // If casting failed (e.g. it was an int), it comes here as int.
        if (is_array($value)) {
            return $value;
        }

        // If it's a number (e.g. 1), assume it implies quantity for the main room type
        if (is_numeric($value)) {
            return [$this->room_type_id => (int) $value];
        }

        // If null or other garbage, return empty array to prevent crashes
        return [];
    }

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function roomType()
    {
        return $this->belongsTo(RoomType::class);
    }

    public function guests()
    {
        return $this->hasMany(\App\Models\BookingGuest::class);
    }

    public function assignedRooms()
    {
        return $this->belongsToMany(Room::class, 'booking_room')->withTimestamps();
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

    public function getGuestNameAttribute()
    {
        return $this->guests->first()->name ?? 'Guest';
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function isCorporate()
    {
        return $this->company_id !== null;
    }

    public function scopeGroup($query, $groupId)
    {
        return $query->where('group_id', $groupId);
    }
}
