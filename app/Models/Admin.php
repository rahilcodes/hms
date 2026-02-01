<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Admin extends Authenticatable
{
    use SoftDeletes;

    protected $fillable = [
        'hotel_id',
        'name',
        'email',
        'password',
        'role',
    ];

    const ROLE_SUPER_ADMIN = 'super_admin';
    const ROLE_ADMIN = 'admin';
    const ROLE_RECEPTIONIST = 'receptionist';
    const ROLE_REVENUE = 'revenue';
    const ROLE_HOUSEKEEPING = 'housekeeping';

    public function isRole($role)
    {
        return $this->role === $role;
    }

    public function isSuperAdmin()
    {
        return $this->role === self::ROLE_SUPER_ADMIN;
    }

    protected $hidden = ['password'];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function activities()
    {
        return $this->hasMany(ActivityLog::class);
    }
}
