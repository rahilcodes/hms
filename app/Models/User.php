<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // --- TITANIUM SOVEREIGN: ROLES ---
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }
    public function isStaff(): bool
    {
        return $this->hasRole('staff');
    }
    public function isGuest(): bool
    {
        return $this->hasRole('guest');
    }

    // --- TITANIUM SOVEREIGN: WALLET ---
    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    /**
     * Get or create the user's wallet.
     */
    public function getWalletAttribute()
    {
        if (!$this->relationLoaded('wallet')) {
            $this->load('wallet');
        }

        return $this->getRelation('wallet') ?? $this->wallet()->create([
            'balance' => 0,
            'currency' => site('currency', 'INR'), // Use global setting
        ]);
    }
}
