<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;

class PasswordExpiredMiddleware
{
    public function handle($request, Closure $next)
    {
        $user = Auth::user();

        if ($user && $user->password_expired == 1) {
            Session::put('password_expired', true);
            return Redirect::route('password.expired');
        }

        return $next($request);
    }
}