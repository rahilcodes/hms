<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaintenanceSchedule extends Model
{
    protected $fillable = [
        'asset_id',
        'title',
        'frequency_days',
        'last_performed_at',
        'next_due_at',
    ];

    protected $casts = [
        'last_performed_at' => 'datetime',
        'next_due_at' => 'datetime',
    ];

    public function asset()
    {
        return $this->belongsTo(\App\Models\Asset::class);
    }

    public function logs()
    {
        return $this->hasMany(\App\Models\MaintenanceLog::class, 'maintenance_schedule_id');
    }
}
