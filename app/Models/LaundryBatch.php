<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaundryBatch extends Model
{
    protected $fillable = [
        'batch_number',
        'vendor_id',
        'status',
        'sent_date',
        'expected_return_date',
        'received_date',
        'total_cost',
        'notes',
    ];

    protected $casts = [
        'sent_date' => 'date',
        'expected_return_date' => 'date',
        'received_date' => 'date',
        'total_cost' => 'decimal:2',
    ];

    public function vendor()
    {
        return $this->belongsTo(LaundryVendor::class, 'vendor_id');
    }

    public function items()
    {
        return $this->hasMany(LaundryItem::class, 'batch_id');
    }
}
