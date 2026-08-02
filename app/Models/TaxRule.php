<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaxRule extends Model
{
    use \App\Traits\BelongsToHotel;

    protected $fillable = [
        'hotel_id',
        'name',
        'type',
        'value',
        'category',
        'min_threshold',
        'max_threshold',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'value' => 'decimal:2',
        'min_threshold' => 'decimal:2',
        'max_threshold' => 'decimal:2',
    ];
}
