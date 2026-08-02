<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaundryVendor extends Model
{
    protected $fillable = [
        'name',
        'contact_person',
        'phone',
        'email',
        'address',
        'rate_card_json',
        'is_active'
    ];

    protected $casts = [
        'rate_card_json' => 'array',
        'is_active' => 'boolean',
    ];

    public function batches()
    {
        return $this->hasMany(LaundryBatch::class, 'vendor_id');
    }
}
