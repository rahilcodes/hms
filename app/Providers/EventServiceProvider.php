<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Events\BookingConfirmed;
use App\Listeners\SendBookingConfirmationWhatsApp;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        BookingConfirmed::class => [
            SendBookingConfirmationWhatsApp::class,
        ],
    ];
}
