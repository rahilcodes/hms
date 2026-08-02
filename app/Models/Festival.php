<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Festival extends Model
{
    protected $fillable = [
        'name',
        'date',
        'end_date',
        'suggested_uplift_percent',
        'region',
    ];

    protected $casts = [
        'date' => 'date',
        'end_date' => 'date',
    ];

    public function scopeUpcoming($query, int $days = 90)
    {
        return $query->whereBetween('date', [now()->startOfDay(), now()->addDays($days)])
            ->orderBy('date');
    }
}
