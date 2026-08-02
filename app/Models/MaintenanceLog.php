<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaintenanceLog extends Model
{
    protected $fillable = [
        'asset_id',
        'room_id',
        'maintenance_schedule_id',
        'technician_name',
        'cost',
        'description',
        'status',
        'photos',
        'completed_at',
    ];

    protected $casts = [
        'photos' => 'array',
        'completed_at' => 'datetime',
        'cost' => 'decimal:2',
    ];

    public function asset()
    {
        return $this->belongsTo(\App\Models\Asset::class);
    }

    public function room()
    {
        return $this->belongsTo(\App\Models\Room::class);
    }

    public function schedule()
    {
        return $this->belongsTo(\App\Models\MaintenanceSchedule::class, 'maintenance_schedule_id');
    }
}
