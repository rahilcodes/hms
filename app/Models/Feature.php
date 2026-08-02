<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feature extends Model
{
    protected $fillable = ['name', 'slug', 'description'];

    public function hotels()
    {
        return $this->belongsToMany(Hotel::class, 'hotel_feature')
            ->withPivot('is_enabled')
            ->withTimestamps();
    }
}
