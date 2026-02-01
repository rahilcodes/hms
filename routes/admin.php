<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\BookingController;
use App\Http\Controllers\Admin\RoomTypeController;
use App\Http\Controllers\Admin\BlockedDateController;
use App\Http\Controllers\Admin\SiteSettingController;
use App\Http\Controllers\Admin\PricingRuleController;
use App\Http\Controllers\Admin\GuestController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\SearchController;
use App\Http\Controllers\Admin\MaintenanceController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\FinancialController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\CouponController;

/*
|--------------------------------------------------------------------------
| Admin Authentication
|--------------------------------------------------------------------------
*/

Route::get('/admin/login', [AuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'login'])->name('admin.login.submit');

/*
|--------------------------------------------------------------------------
| Protected Admin Area (All authenticated admins)
|--------------------------------------------------------------------------
*/

Route::middleware(['admin'])->group(function () {
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/admin/analytics', [DashboardController::class, 'analytics'])->name('admin.analytics');

    // Notifications
    Route::get('/admin/notifications', [App\Http\Controllers\Admin\NotificationController::class, 'index'])->name('admin.notifications.index');
    Route::get('/admin/notifications/check', [App\Http\Controllers\Admin\NotificationController::class, 'check'])->name('admin.notifications.check');
    Route::post('/admin/notifications/mark-all-read', [App\Http\Controllers\Admin\NotificationController::class, 'markAllRead'])->name('admin.notifications.markAllRead');
    Route::get('/admin/pulse', [DashboardController::class, 'pulse'])->name('admin.pulse');
    Route::post('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout');
    Route::get('/admin/search', [SearchController::class, 'global'])->name('admin.search');

    // Housekeeping & Assets
    Route::resource('/admin/assets', \App\Http\Controllers\Admin\AssetController::class, ['as' => 'admin']);

    /*
    |--------------------------------------------------------------------------
    | OPERATIONS (Receptionists, Admins, Super Admins)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:super_admin,admin,receptionist'])->group(function () {
        // FRONT DESK
        Route::middleware(['feature:front-desk'])->group(function () {
            Route::get('/admin/front-desk', [DashboardController::class, 'frontDesk'])->name('admin.front-desk');
            Route::get('/admin/tape-chart', [\App\Http\Controllers\Admin\TapeChartController::class, 'index'])->name('admin.tape-chart.index');
        });

        // GUEST REQUESTS (Concierge)
        Route::get('/admin/guest-requests', [\App\Http\Controllers\Admin\RequestController::class, 'index'])->name('admin.requests.index');
        Route::patch('/admin/guest-requests/{guestRequest}', [\App\Http\Controllers\Admin\RequestController::class, 'update'])->name('admin.requests.update');

        // DINING & MENU
        Route::get('/admin/dining-orders', [\App\Http\Controllers\Admin\DiningOrderController::class, 'index'])->name('admin.dining.orders.index');
        Route::patch('/admin/dining-orders/{order}/status', [\App\Http\Controllers\Admin\DiningOrderController::class, 'updateStatus'])->name('admin.dining.orders.updateStatus');

        Route::get('/admin/menu', [\App\Http\Controllers\Admin\MenuController::class, 'index'])->name('admin.menu.index');
        Route::post('/admin/menu/category', [\App\Http\Controllers\Admin\MenuController::class, 'storeCategory'])->name('admin.menu.category.store');
        Route::post('/admin/menu/item', [\App\Http\Controllers\Admin\MenuController::class, 'storeItem'])->name('admin.menu.item.store');
        Route::patch('/admin/menu/item/{item}', [\App\Http\Controllers\Admin\MenuController::class, 'updateItem'])->name('admin.menu.item.update');
        Route::patch('/admin/menu/item/{item}/toggle', [\App\Http\Controllers\Admin\MenuController::class, 'toggleItem'])->name('admin.menu.item.toggle');
        Route::delete('/admin/menu/item/{item}', [\App\Http\Controllers\Admin\MenuController::class, 'deleteItem'])->name('admin.menu.item.delete');

        // CMS PAGES
        Route::resource('/admin/pages', \App\Http\Controllers\Admin\PageController::class)->names('admin.pages');

        // BOOKINGS
        Route::get('/admin/bookings/calendar', [BookingController::class, 'calendar'])->name('admin.bookings.calendar');
        Route::get('/admin/bookings/calendar/data', [BookingController::class, 'calendarData'])->name('admin.bookings.calendar.data');
        Route::get('/admin/bookings/{booking}/invoice', [BookingController::class, 'invoice'])->name('admin.bookings.invoice');
        Route::get('/admin/bookings', [BookingController::class, 'index'])->name('admin.bookings.index');
        Route::get('/admin/bookings/create', [BookingController::class, 'create'])->name('admin.bookings.create');
        Route::post('/admin/bookings', [BookingController::class, 'store'])->name('admin.bookings.store');
        Route::get('/admin/bookings/{booking}', [BookingController::class, 'show'])->name('admin.bookings.show');
        Route::post('/admin/bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('admin.bookings.cancel');
        Route::post('/admin/bookings/{booking}/mark-paid', [BookingController::class, 'markPaid'])->name('admin.bookings.markPaid');
        Route::post('/admin/bookings/{booking}/check-in', [BookingController::class, 'checkIn'])->name('admin.bookings.checkIn');
        Route::post('/admin/bookings/{booking}/check-out', [BookingController::class, 'checkOut'])->name('admin.bookings.checkOut');
        Route::patch('/admin/bookings/{booking}/reschedule', [BookingController::class, 'reschedule'])->name('admin.bookings.reschedule');

        // GUESTS (CRM)
        Route::middleware(['feature:crm'])->group(function () {
            Route::get('/admin/guests', [GuestController::class, 'index'])->name('admin.guests.index');
            Route::get('/admin/guests/{phone}', [GuestController::class, 'show'])->name('admin.guests.show');
            Route::put('/admin/guests/{phone}', [GuestController::class, 'update'])->name('admin.guests.update');
        });

        // NIGHT AUDIT
        Route::get('/admin/night-audit', [\App\Http\Controllers\Admin\NightAuditController::class, 'index'])->name('admin.night-audit.index');
        Route::post('/admin/night-audit/run', [\App\Http\Controllers\Admin\NightAuditController::class, 'perform'])->name('admin.night-audit.run');

        // INVENTORY MATRIX
        Route::middleware(['feature:inventory'])->group(function () {
            Route::get('/admin/inventory', [InventoryController::class, 'index'])->name('admin.inventory.index');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | REVENUE & INVENTORY (Revenue Team, Admins, Super Admins)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:super_admin,admin,revenue'])->group(function () {
        // ROOM TYPES
        Route::resource('/admin/room-types', RoomTypeController::class, ['as' => 'admin'])->except(['destroy', 'show']);
        Route::post('/admin/room-types/{roomType}/remove-image', [RoomTypeController::class, 'removeImage'])->name('admin.room-types.remove-image');
        Route::post('/admin/room-types/{roomType}/reorder-images', [RoomTypeController::class, 'reorderImages'])->name('admin.room-types.reorder-images');

        // BLOCKED DATES
        Route::resource('/admin/blocked-dates', BlockedDateController::class, ['as' => 'admin'])->only(['index', 'create', 'store', 'destroy']);

        // PRICING RULES
        Route::resource('/admin/pricing-rules', PricingRuleController::class, ['as' => 'admin'])->only(['index', 'create', 'store', 'destroy']);

        // SERVICES (UPSELLS)
        Route::resource('/admin/services', ServiceController::class, ['as' => 'admin'])->except(['show', 'create', 'edit']);

        // CORPORATE MANAGEMENT
        Route::resource('/admin/companies', CompanyController::class, ['as' => 'admin'])->except(['show']);

        // COUPON MANAGEMENT
        Route::resource('/admin/coupons', CouponController::class, ['as' => 'admin'])->except(['show']);

        // YIELD INTELLIGENCE
        Route::middleware(['feature:financials'])->group(function () {
            Route::get('/admin/yield', [\App\Http\Controllers\Admin\YieldController::class, 'index'])->name('admin.yield.index');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | MAINTENANCE (Housekeeping, Receptionists, Admins, Super Admins)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:super_admin,admin,housekeeping,receptionist', 'feature:housekeeping'])->group(function () {
        Route::get('/admin/maintenance', [MaintenanceController::class, 'index'])->name('admin.maintenance.index');
        Route::get('/admin/maintenance/create', [MaintenanceController::class, 'create'])->name('admin.maintenance.create');
        Route::post('/admin/maintenance', [MaintenanceController::class, 'store'])->name('admin.maintenance.store');
        Route::patch('/admin/maintenance/{maintenance}/status', [MaintenanceController::class, 'updateStatus'])->name('admin.maintenance.updateStatus');

        // HOUSEKEEPING
        Route::get('/admin/housekeeping', [\App\Http\Controllers\Admin\HousekeepingController::class, 'index'])->name('admin.housekeeping.index');
        Route::patch('/admin/housekeeping/{room}/status', [\App\Http\Controllers\Admin\HousekeepingController::class, 'updateStatus'])->name('admin.housekeeping.updateStatus');
        Route::put('/admin/housekeeping/{room}', [\App\Http\Controllers\Admin\HousekeepingController::class, 'update'])->name('admin.housekeeping.update');

        // LOST & FOUND
        Route::resource('/admin/lost-found', \App\Http\Controllers\Admin\LostFoundController::class, ['as' => 'admin'])
            ->parameters(['lost-found' => 'lost_found_item']);

        // LINEN & LAUNDRY
        Route::resource('/admin/linen', \App\Http\Controllers\Admin\LinenController::class, ['as' => 'admin'])->except(['create', 'edit', 'show']);
        Route::resource('/admin/laundry/vendors', \App\Http\Controllers\Admin\LaundryVendorController::class, ['as' => 'admin.laundry'])->except(['create', 'edit', 'show']);
        Route::resource('/admin/laundry/batches', \App\Http\Controllers\Admin\LaundryBatchController::class, ['as' => 'admin.laundry']);
    });

    /*
    |--------------------------------------------------------------------------
    | SYSTEM (Super Admins Only)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:super_admin'])->group(function () {
        Route::get('/admin/subscription', [App\Http\Controllers\Admin\SubscriptionController::class, 'index'])->name('admin.subscription.index');
        Route::get('/admin/audit-logs', [AuditLogController::class, 'index'])->name('admin.audit-logs.index');
        Route::get('/admin/site-settings', [SiteSettingController::class, 'index'])->name('admin.site-settings.index');
        Route::post('/admin/site-settings', [SiteSettingController::class, 'update'])->name('admin.site-settings.update');

        // STAFF MANAGEMENT
        Route::resource('/admin/staff', StaffController::class, ['as' => 'admin']);

        // FINANCIALS
        Route::middleware(['feature:financials'])->group(function () {
            Route::get('/admin/financials', [FinancialController::class, 'index'])->name('admin.financials.index');
            Route::get('/admin/financials/export', [FinancialController::class, 'export'])->name('admin.financials.export');
        });
    });
});
