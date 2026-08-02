<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Admin extends Authenticatable
{
    use SoftDeletes, \App\Traits\BelongsToHotel;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'permissions',
    ];

    protected $casts = [
        'permissions' => 'array',
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

    /**
     * Granular staff permission check. Super admins and admins always pass.
     * Keys: can_discount, can_void, can_override_rate, max_discount_percent.
     */
    public function hasPermission(string $key): bool
    {
        if ($this->isSuperAdmin() || $this->role === self::ROLE_ADMIN) {
            return true;
        }
        return (bool) data_get($this->permissions, $key, false);
    }

    public function maxDiscountPercent(): float
    {
        if ($this->isSuperAdmin() || $this->role === self::ROLE_ADMIN) {
            return 100;
        }
        if (!$this->hasPermission('can_discount')) {
            return 0;
        }
        return (float) data_get($this->permissions, 'max_discount_percent', 10);
    }

    protected $hidden = ['password'];


    public function activities()
    {
        return $this->hasMany(ActivityLog::class);
    }
}
