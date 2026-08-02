<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use \App\Traits\BelongsToHotel;

    protected $fillable = [
        'hotel_id', 'category', 'amount', 'description',
        'reference_number', 'expense_date', 'payment_method',
        'status', 'metadata'
    ];

    protected $casts = [
        'expense_date' => 'date',
        'amount' => 'decimal:2',
        'metadata' => 'array'
    ];
}
