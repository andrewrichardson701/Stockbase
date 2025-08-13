<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class ImpersonationMiddleware
{
    public function handle($request, Closure $next)
    {
        if (Session::has('impersonate_id')) {
            Auth::onceUsingId(Session::get('impersonate_id'));
        }

        return $next($request);
    }
}