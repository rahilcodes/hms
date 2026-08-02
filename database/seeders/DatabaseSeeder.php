<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            HotelDataSeeder::class,
            FeatureSeeder::class,
            SiteSettingsSeeder::class,
            PageSeeder::class,
            AdditionalPagesSeeder::class,
            PeopleSeeder::class,
            BookingSeeder::class,
            OperationsSeeder::class,
            DiningSeeder::class,
            FestivalSeeder::class,
            IndiaOpsSeeder::class,
        ]);
    }
}
