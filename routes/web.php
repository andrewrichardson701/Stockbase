<?php

use Illuminate\Support\Facades\Route;

use App\Http\Middleware\SecurityMiddleware;
use App\Http\Middleware\AddHeadData;
use App\Http\Middleware\TwoFactorRedirectMiddleware;
use App\Http\Middleware\PasswordExpiredMiddleware;
use App\Http\Middleware\CheckSessionMiddleware;

Route::middleware([AddHeadData::class])->group(function () {

    // default auth page
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->middleware(['auth', 'verified'])->name('dashboard');

    // All routes that require authentication and security checks
    Route::middleware([
        PasswordExpiredMiddleware::class,
        TwoFactorRedirectMiddleware::class,
        SecurityMiddleware::class,
    ])->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Authenticated routes
        |--------------------------------------------------------------------------
        */

        Route::middleware([
            CheckSessionMiddleware::class,
            'auth'
        ])->group(function () {

            require __DIR__.'/web/index.php';
            require __DIR__.'/web/profile.php';
            require __DIR__.'/web/impersonation.php';
            require __DIR__.'/web/assets.php';
            require __DIR__.'/web/stock.php';
            require __DIR__.'/web/containers.php';
            require __DIR__.'/web/admin.php';
            require __DIR__.'/web/changelog.php';
            require __DIR__.'/web/cables.php';
            require __DIR__.'/web/ajax.php';
        });

        /*
        |--------------------------------------------------------------------------
        | Public routes
        |--------------------------------------------------------------------------
        */

        require __DIR__.'/web/public.php';
    });
});

require __DIR__.'/auth.php';