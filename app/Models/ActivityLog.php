<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
        'admin_id',
        'action',
        'description',
        'target_type',
        'target_id',
        'ip_address',
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    /**
     * Get the parent target model (Booking, RoomType, etc.)
     */
    public function target()
    {
        return $this->morphTo(null, 'target_type', 'target_id');
    }

    public static function log($action, $description = null, $target = null)
    {
        return self::create([
            'admin_id' => auth('admin')->id(),
            'action' => $action,
            'description' => $description,
            'target_type' => $target ? get_class($target) : null,
            'target_id' => $target ? $target->id : null,
            'ip_address' => request()->ip(),
        ]);
    }
}
