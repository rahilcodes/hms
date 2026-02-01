<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withProviders([
        App\Providers\AppServiceProvider::class,
        App\Providers\EventServiceProvider::class,
    ])
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\CheckMaintenanceMode::class,
        ]);

        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminAuth::class,
            'role' => \App\Http\Middleware\CheckRole::class,
            'guest_portal' => \App\Http\Middleware\GuestPortalMiddleware::class,
            'feature' => \App\Http\Middleware\CheckHotelFeature::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (Throwable $e, \Illuminate\Http\Request $request) {
            if ($e instanceof \Exception && !$request->expectsJson() && app()->environment('production')) {
                return response()->view('errors.concierge', [], 500);
            }
        });
    })
    ->create();
