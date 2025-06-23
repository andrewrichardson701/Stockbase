<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\ImpersonationMiddleware;
use App\Http\Middleware\TrustProxies;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
        $middleware->alias([
            'check.permission' => \App\Http\Middleware\PermissionsMiddleware::class,
            'impersonation' => ImpersonationMiddleware::class,
            'TrustProxies' => TrustProxies::class,
        ]);

        $middleware->web([
            TrustProxies::class,
            ImpersonationMiddleware::class, // <== This runs on every web request
        ]);
        
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
