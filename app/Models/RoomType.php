<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RoomType extends Model
{
    use SoftDeletes, \App\Traits\BelongsToHotel;

    protected $fillable = [
        'name',
        'image',
        'description',
        'amenities',
        'total_rooms',
        'base_price',
        'base_occupancy',
        'max_extra_persons',
        'extra_person_price',
        'gallery_json',
        'day_use_enabled',
        'day_use_price_4h',
        'day_use_price_8h',
        'child_price',
        'extra_mattress_price',
    ];

    protected $casts = [
        'amenities' => 'array',
        'gallery_json' => 'array',
        'day_use_enabled' => 'boolean',
    ];


    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function blockedDates()
    {
        return $this->hasMany(BlockedDate::class);
    }

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    public function getPriceAttribute()
    {
        return $this->base_price;
    }

    public function getImageUrlAttribute()
    {
        if ($this->image && \Illuminate\Support\Facades\Storage::disk('public')->exists($this->image)) {
            return asset('storage/' . $this->image);
        }

        // Fallback to high-quality Unsplash image based on name
        $slug = strtolower(str_replace(' ', '-', $this->name));
        return "https://images.unsplash.com/photo-1618773928121-c32242e63f39?q=80&w=2670&auto=format&fit=crop&room={$slug}";
    }
}
