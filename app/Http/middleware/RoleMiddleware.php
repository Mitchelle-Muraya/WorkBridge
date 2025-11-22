<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle($request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect('/login')->with('error', 'You must be logged in.');
        }

        // ✅ Restrict access based on role
        if (!in_array(Auth::user()->role, $roles)) {
            return redirect('/')->with('error', '🚫 Access denied.');
        }

        return $next($request);
    }
}
