<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, $role)
    {
        // Check for all guards
        $guards = ['worker', 'client', 'admin'];

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();

                // If the logged-in user’s role or guard matches, allow access
                if ($user->role === $role || $guard === $role) {
                    return $next($request);
                }

                abort(403, 'Unauthorized access.');
            }
        }

        return redirect()->route('login');
    }
}
