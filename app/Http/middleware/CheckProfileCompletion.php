<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckProfileCompletion
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        // Don’t interfere with onboarding itself
        if ($request->is('onboarding*')) {
            return $next($request);
        }

        // If user profile is incomplete, redirect to onboarding
        if ($user && !$user->is_profile_complete) {
            return redirect()->route('profile.onboarding');
        }

        return $next($request);
    }
}
