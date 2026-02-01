<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentWebhookController;

Route::post('/razorpay/webhook', [PaymentWebhookController::class, 'handle']);
Route::post('/promo/validate', [\App\Http\Controllers\CouponController::class, 'validatePromo']);
