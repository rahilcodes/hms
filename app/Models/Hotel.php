<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Hotel extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'phone',
        'email',
        'address',
    ];

    public function admins()
    {
        return $this->hasMany(Admin::class);
    }

    public function roomTypes()
    {
        return $this->hasMany(RoomType::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function subscription()
    {
        return $this->hasOne(Subscription::class);
    }

    public function features()
    {
        return $this->belongsToMany(Feature::class, 'hotel_feature')
            ->withPivot('is_enabled')
            ->withTimestamps();
    }

    public function hasFeature($slug)
    {
        // Cache this in production
        $feature = $this->features()->where('slug', $slug)->first();
        return $feature && $feature->pivot->is_enabled;
    }
}
