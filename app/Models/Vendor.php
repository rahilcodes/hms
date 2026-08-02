<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use \App\Traits\BelongsToHotel;

    protected $fillable = [
        'hotel_id', 'name', 'contact_person', 'phone',
        'email', 'category', 'current_balance'
    ];

    protected $casts = [
        'current_balance' => 'decimal:2',
    ];

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }
}
