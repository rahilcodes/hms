<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
use Illuminate\Support\Facades\Schedule;

Schedule::command('bookings:expire')->everyFiveMinutes();
Schedule::command('guest:send-reminders')->dailyAt('09:00');
// Post nightly room rent + GST tax rules to guest folios (idempotent)
Schedule::command('audit:night')->dailyAt('03:00');
