<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB;

class PermissionsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $permission
     * @return mixed
     */
    public function handle($request, Closure $next, $permissions)
    {
        $user = Auth::user();

        if (!$user) {
            abort(Response::HTTP_FORBIDDEN, 'Unauthorized');
        }

        // Get the user's permissions from the users_permissions table
        $user_permissions = DB::table('users_permissions')
            ->where('id', $user->id)
            ->first();

        if (!$user_permissions) {
            abort(Response::HTTP_FORBIDDEN, 'No permissions found.');
        }

        // Convert comma-separated string to array
        $permissionsArray = explode(',', $permissions);

        // Check if user has at least one of the requested permissions
        $hasAnyPermission = collect($permissionsArray)->some(function ($perm) use ($user_permissions) {
            return $user_permissions->{$perm} ?? false;
        });

        if (!$hasAnyPermission) {
            abort(Response::HTTP_FORBIDDEN, 'Permission Denied.');
        }

        return $next($request);
    }
}