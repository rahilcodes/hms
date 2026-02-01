<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PaymentController;

/*
|--------------------------------------------------------------------------
| Guest Website Routes
|--------------------------------------------------------------------------
*/

// Home page (search-only homepage)
Route::get('/', [HomeController::class, 'index'])->name('home');

// Static Content Pages
use App\Http\Controllers\PageController;
Route::get('/about', [PageController::class, 'about'])->name('pages.about');
Route::get('/contact', [PageController::class, 'contact'])->name('pages.contact');
Route::get('/dining', [PageController::class, 'dining'])->name('pages.dining');
Route::get('/gallery', [PageController::class, 'gallery'])->name('pages.gallery');
Route::get('/page/{slug}', [PageController::class, 'show'])->name('pages.show');

// Rooms page (availability + selection UI)
use App\Http\Controllers\RoomsController;

Route::get('/rooms', [RoomsController::class, 'index'])->name('rooms');
Route::get('/rooms/{roomType}', [RoomsController::class, 'show'])->name('rooms.show');


// Checkout page (final confirmation)
use App\Http\Controllers\CheckoutController;

Route::get('/checkout', [CheckoutController::class, 'show'])->name('checkout.show');
Route::get('/checkout/add-room', [CheckoutController::class, 'addRoom'])->name('checkout.add-room');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

// Thank you / confirmation page
Route::get('/thank-you', function () {
    return view('thank-you.index');
})->name('thank-you');

// Online payment page
Route::get('/pay/{booking}', [PaymentController::class, 'pay'])->name('payment.pay');

// Digital Registration Card
Route::get('/check-in/{uuid}', [App\Http\Controllers\GuestRegistrationController::class, 'show'])->name('guest.registration.show');
Route::post('/check-in/{uuid}', [App\Http\Controllers\GuestRegistrationController::class, 'update'])->name('guest.registration.update');

/*
|--------------------------------------------------------------------------
| Guest Portal (Passwordless Access)
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\GuestPortalController;

Route::get('/portal/login', [GuestPortalController::class, 'showLogin'])->name('guest.login');
Route::post('/portal/login', [GuestPortalController::class, 'verify'])->middleware('throttle:5,1')->name('guest.verify');

Route::middleware(['guest_portal'])->group(function () {
    Route::get('/portal/dashboard', [GuestPortalController::class, 'dashboard'])->name('guest.dashboard');
    Route::get('/portal/select', [GuestPortalController::class, 'select'])->name('guest.select');
    Route::get('/portal/switch/{booking}', [GuestPortalController::class, 'switch'])->name('guest.switch');
    Route::get('/portal/dining', [App\Http\Controllers\GuestDiningController::class, 'index'])->name('guest.dining');
    Route::post('/portal/dining/order', [App\Http\Controllers\GuestDiningController::class, 'store'])->name('guest.dining.order');
    Route::get('/portal/addons', [GuestPortalController::class, 'addons'])->name('guest.addons');
    Route::post('/portal/addons/book', [GuestPortalController::class, 'bookAddon'])->name('guest.book-addon');
    Route::post('/portal/request', [App\Http\Controllers\GuestRequestController::class, 'store'])->name('guest.request.store');
    Route::post('/portal/logout', [GuestPortalController::class, 'logout'])->name('guest.logout');
});

require __DIR__ . '/admin.php';
require __DIR__ . '/titanium.php';
