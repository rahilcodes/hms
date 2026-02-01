<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NightAudit extends Model
{
    protected $fillable = [
        'hotel_id',
        'audit_date',
        'performed_by_admin_id',
        'revenue_total',
        'occupancy_rate',
        'no_shows_count',
        'checked_out_count',
        'status',
        'notes',
    ];

    protected $casts = [
        'audit_date' => 'date',
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'performed_by_admin_id');
    }
}
