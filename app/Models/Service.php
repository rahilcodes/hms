<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use \App\Traits\BelongsToHotel;
    protected $fillable = ['name', 'description', 'price', 'price_unit', 'icon_class', 'is_active', 'constraints'];

    protected $casts = [
        'constraints' => 'array',
        'is_active' => 'boolean',
    ];
    public function hasConstraint($key)
    {
        return isset($this->constraints[$key]);
    }

    public function getConstraint($key, $default = null)
    {
        return $this->constraints[$key] ?? $default;
    }
}
