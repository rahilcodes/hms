<?php

namespace App\Traits;

use App\Scopes\HotelScope;
use Illuminate\Support\Facades\Session;

trait BelongsToHotel
{
    public static function bootBelongsToHotel()
    {
        static::addGlobalScope(new HotelScope);

        static::creating(function ($model) {
            if (!$model->hotel_id) {
                $hotelId = null;

                if (auth('admin')->check()) {
                    $hotelId = auth('admin')->user()->hotel_id;
                }
                elseif (Session::has('hotel_id')) {
                    $hotelId = Session::get('hotel_id');
                }

                if ($hotelId) {
                    $model->hotel_id = $hotelId;
                }
            }
        });
    }

    public function hotel()
    {
        return $this->belongsTo(\App\Models\Hotel::class);
    }
}
