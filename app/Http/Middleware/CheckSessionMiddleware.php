<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Session;

class CheckSessionMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if a session exists
        if (!Session::getId()) {
            // Redirect to login page if not set
            return redirect()->route('index');
        }

        // Otherwise, continue processing
        return $next($request);
    }
}