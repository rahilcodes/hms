<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = [
        'hotel_id',
        'plan_name',
        'price',
        'billing_cycle',
        'starts_at',
        'next_billing_date',
        'status',
        'features_snapshot',
    ];

    protected $casts = [
        'starts_at' => 'date',
        'next_billing_date' => 'date',
        'features_snapshot' => 'array',
        'price' => 'decimal:2',
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }
}
