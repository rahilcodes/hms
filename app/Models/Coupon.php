<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = [
        'code',
        'type',
        'value',
        'min_spend',
        'max_discount',
        'starts_at',
        'expires_at',
        'usage_limit',
        'used_count',
        'is_active',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function isValidFor($amount)
    {
        if (!$this->is_active)
            return false;

        $now = now();
        if ($this->starts_at && $now->lt($this->starts_at))
            return false;
        if ($this->expires_at && $now->gt($this->expires_at))
            return false;

        if ($this->usage_limit !== null && $this->used_count >= $this->usage_limit)
            return false;

        if ($amount < $this->min_spend)
            return false;

        return true;
    }

    public function calculateDiscount($amount)
    {
        if ($this->type === 'fixed') {
            return min($this->value, $amount);
        }

        $discount = ($amount * $this->value) / 100;

        if ($this->max_discount !== null) {
            $discount = min($discount, $this->max_discount);
        }

        return round($discount, 2);
    }
}
