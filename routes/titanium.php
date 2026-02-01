<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Titanium\AuthController;
use App\Http\Controllers\Titanium\DashboardController;
use App\Http\Controllers\Titanium\HotelController;

/*
|--------------------------------------------------------------------------
| Titanium Master Admin Routes
|--------------------------------------------------------------------------
*/

Route::prefix('titanium')->name('titanium.')->group(function () {

    // Auth Routes
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Protected Routes
    Route::middleware(['auth:titanium'])->group(function () {
        // Dashboard
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // Hotels Management
        Route::resource('hotels', HotelController::class)->only(['create', 'store', 'show']);
        Route::post('/hotels/{hotel}/impersonate', [HotelController::class, 'impersonate'])->name('hotels.impersonate');
        // Distinct path to avoid resource collision
        Route::post('/hotels/{hotel}/features/toggle', [HotelController::class, 'toggleFeature'])->name('hotels.features.toggle');
        Route::patch('/hotels/{hotel}/subscription', [HotelController::class, 'updateSubscription'])->name('hotels.subscription.update');

        // Subscription
        Route::get('/subscription', [App\Http\Controllers\Titanium\SubscriptionController::class, 'index'])->name('subscription.index');
        Route::post('/subscription', [App\Http\Controllers\Titanium\SubscriptionController::class, 'update'])->name('subscription.update');
        Route::post('/subscription/toggle', [App\Http\Controllers\Titanium\SubscriptionController::class, 'toggle'])->name('subscription.toggle');

        // Notifications
        Route::post('/notifications', [App\Http\Controllers\Titanium\NotificationController::class, 'store'])->name('notifications.store');
    });
});
