<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SiteSettingsSeeder::class,
            HotelDataSeeder::class,
            PeopleSeeder::class,
            BookingSeeder::class,
            OperationsSeeder::class,
        ]);
    }
}
