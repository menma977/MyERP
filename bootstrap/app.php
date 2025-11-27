<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api/v1/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware(['api'])->prefix('api/v1')->name('api.v1.')->group(function () {
                require base_path('routes/api/v1/approvals.php');
                require base_path('routes/api/v1/items.php');
                require base_path('routes/api/v1/purchases.php');
                require base_path('routes/api/v1/sales.php');
                require base_path('routes/api/v1/transactions.php');
                require base_path('routes/api/v1/vendors.php');
            });
        }
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
