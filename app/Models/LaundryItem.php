<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\LaundryBatch;
use App\Models\LinenType;

class LaundryItem extends Model
{
    protected $fillable = [
        'batch_id',
        'linen_type_id',
        'quantity_sent',
        'quantity_received',
        'quantity_rejected',
        'cost_incurred',
    ];

    public function batch()
    {
        return $this->belongsTo(LaundryBatch::class);
    }

    public function linenType()
    {
        return $this->belongsTo(LinenType::class);
    }
}
