<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    protected $fillable = [
        'room_type_id',
        'room_id',
        'name',
        'type',
        'brand',
        'model',
        'serial_number',
        'purchase_date',
        'warranty_expiry',
        'status',
        'qr_code',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'warranty_expiry' => 'date',
    ];

    public function room()
    {
        return $this->belongsTo(\App\Models\Room::class);
    }

    public function roomType()
    {
        return $this->belongsTo(\App\Models\RoomType::class);
    }

    public function maintenanceSchedules()
    {
        return $this->hasMany(MaintenanceSchedule::class);
    }

    public function maintenanceLogs()
    {
        return $this->hasMany(MaintenanceLog::class);
    }
}
