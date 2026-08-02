<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LinenType extends Model
{
    protected $fillable = [
        'name',
        'category',
        'par_level',
        'total_stock',
        'cost_per_unit',
    ];

    protected $casts = [
        'cost_per_unit' => 'decimal:2',
    ];
}
