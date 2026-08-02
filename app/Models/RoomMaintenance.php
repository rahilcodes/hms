<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomMaintenance extends Model
{
    protected $fillable = [
        'room_type_id',
        'start_date',
        'end_date',
        'rooms_count',
        'reason',
        'status',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function roomType()
    {
        return $this->belongsTo(RoomType::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'ongoing');
    }
}
