<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Session;

class HotelScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        $hotelId = $this->getHotelId();

        if ($hotelId) {
            $builder->where($model->getTable() . '.hotel_id', $hotelId);
        }
    }

    protected function getHotelId()
    {
        // 1. Session is the primary, non-recursive source of truth
        if (Session::has('hotel_id')) {
            return Session::get('hotel_id');
        }

        // 2. Fallback to Auth only if we aren't already resolving the user
        // We use hasUser() to check if the user is already resolved to avoid recursion
        if (app()->bound('auth') && auth('admin')->hasUser()) {
            return auth('admin')->user()->hotel_id;
        }

        return null;
    }
}
