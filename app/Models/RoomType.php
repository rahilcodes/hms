<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RoomType extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'hotel_id',
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
    ];

    protected $casts = [
        'amenities' => 'array',
        'gallery_json' => 'array',
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

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
