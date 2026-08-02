<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class PlatformAdmin extends Authenticatable
{
    use SoftDeletes;

    protected $fillable = [
        'hotel_id',
        'name',
        'email',
        'password',
        'role',
        'last_login_at',
    ];

    protected $hidden = ['password'];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    protected $casts = [
        'last_login_at' => 'datetime',
    ];

    public function isSuperOwner()
    {
        return $this->role === 'super_owner';
    }
}
