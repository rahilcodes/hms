<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminNotification extends Model
{
    protected $fillable = [
        'hotel_id',
        'title',
        'message',
        'type',
        'is_read',
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }
}
