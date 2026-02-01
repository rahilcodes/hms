<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PlatformAdmin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class TitaniumSeeder extends Seeder
{
    public function run(): void
    {
        if (!Schema::hasTable('platform_admins')) {
            return;
        }

        PlatformAdmin::updateOrCreate(
            ['email' => 'titanium@hotel.com'],
            [
                'name' => 'Titanium Master',
                'password' => Hash::make('password'),
                'role' => 'super_owner',
                'last_login_at' => now(),
            ]
        );
    }
}
