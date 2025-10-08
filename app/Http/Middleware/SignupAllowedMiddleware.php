<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use App\Models\GeneralModel;

class SignupAllowedMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $config = GeneralModel::config();
        $signup_allowed = $config['signup_allowed'];
        // Check if a session exists
        if (!$signup_allowed) {
            // Redirect to login page if not set
            return redirect()->route('index');
        }

        // Otherwise, continue processing
        return $next($request);
    }
}