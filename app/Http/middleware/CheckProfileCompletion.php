<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckProfileCompletion
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        // ✅ Allow onboarding page to be seen by users who are not complete
        if ($request->is('onboarding*')) {
            return $next($request);
        }

        // ✅ If the user is not complete, redirect to onboarding
        if ($user && !$user->is_profile_complete) {
            return redirect()->route('profile.onboarding');
        }

        return $next($request);
    }
}
