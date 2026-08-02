<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LostAndFound extends Model
{
    protected $table = 'lost_and_found'; // Helper since table name is non-standard plural

    protected $fillable = [
        'found_location',
        'description',
        'category',
        'found_by',
        'found_at',
        'status',
        'guest_details',
    ];

    protected $casts = [
        'found_at' => 'datetime',
    ];
}
