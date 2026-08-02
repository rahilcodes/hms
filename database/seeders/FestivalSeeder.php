<?php

namespace Database\Seeders;

use App\Models\Festival;
use Illuminate\Database\Seeder;

class FestivalSeeder extends Seeder
{
    /**
     * Major Indian high-demand dates. Lunar-calendar dates for 2027 are
     * approximate — owners can add/adjust from the Yield screen.
     */
    public function run(): void
    {
        $festivals = [
            // 2026
            ['name' => 'Raksha Bandhan', 'date' => '2026-08-28', 'uplift' => 15],
            ['name' => 'Ganesh Chaturthi', 'date' => '2026-09-14', 'end' => '2026-09-23', 'uplift' => 20],
            ['name' => 'Navratri / Durga Puja', 'date' => '2026-10-11', 'end' => '2026-10-19', 'uplift' => 20],
            ['name' => 'Dussehra', 'date' => '2026-10-20', 'uplift' => 25],
            ['name' => 'Diwali', 'date' => '2026-11-06', 'end' => '2026-11-10', 'uplift' => 30],
            ['name' => 'Christmas Week', 'date' => '2026-12-24', 'end' => '2026-12-26', 'uplift' => 30],
            ['name' => 'New Year Eve', 'date' => '2026-12-30', 'end' => '2027-01-01', 'uplift' => 40],

            // 2027
            ['name' => 'Makar Sankranti / Pongal', 'date' => '2027-01-14', 'uplift' => 15],
            ['name' => 'Republic Day Weekend', 'date' => '2027-01-26', 'uplift' => 15],
            ['name' => 'Holi', 'date' => '2027-03-22', 'end' => '2027-03-23', 'uplift' => 25],
            ['name' => 'Eid al-Fitr', 'date' => '2027-03-10', 'uplift' => 15],
            ['name' => 'Good Friday / Easter', 'date' => '2027-03-26', 'end' => '2027-03-28', 'uplift' => 15],
            ['name' => 'Summer Vacation Peak', 'date' => '2027-05-15', 'end' => '2027-06-15', 'uplift' => 20],
            ['name' => 'Independence Day Weekend', 'date' => '2027-08-14', 'end' => '2027-08-15', 'uplift' => 15],
            ['name' => 'Diwali', 'date' => '2027-10-27', 'end' => '2027-10-31', 'uplift' => 30],
            ['name' => 'Christmas Week', 'date' => '2027-12-24', 'end' => '2027-12-26', 'uplift' => 30],
            ['name' => 'New Year Eve', 'date' => '2027-12-30', 'end' => '2028-01-01', 'uplift' => 40],
        ];

        foreach ($festivals as $f) {
            Festival::updateOrCreate(
                ['name' => $f['name'], 'date' => $f['date']],
                [
                    'end_date' => $f['end'] ?? null,
                    'suggested_uplift_percent' => $f['uplift'],
                    'region' => null,
                ]
            );
        }
    }
}
