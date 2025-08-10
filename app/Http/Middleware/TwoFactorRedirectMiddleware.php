<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;

class TwoFactorRedirectMiddleware
{
    public function handle($request, Closure $next)
    {
        $status = Session::get('two_factor_request');

        // Allow through if 2FA is already skipped or completed
        if ($status === 'skip' || $status === 'complete') {
            return $next($request);
        }

        // Prevent redirect loop: allow access if already on 2FA routes
        if ($request->routeIs(['two-factor.challenge', 'two-factor.setup', 'two-factor.verify'])) {
            return $next($request);
        }

        // Only store intended URL if it wasn't already set by the login redirect
        if (! Session::has('url.intended')) {
            Session::put('url.intended', $request->fullUrl());
        }

        // Decide where to send the user
        if ($status === 'challenge') {
            return Redirect::route('two-factor.challenge');
        }

        if ($status === 'setup') {
            return Redirect::route('two-factor.setup');
        }

        return $next($request);
    }
}