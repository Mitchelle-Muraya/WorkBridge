<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Worker;

class AuthController extends Controller
{
    /**
     * Show login page
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Show registration page
     */
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    /**
     * Handle user registration
     */
    public function register(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:6|confirmed',
    ]);

    // Create user record (instead of directly creating worker)
    $user = \App\Models\User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => bcrypt($request->password),
        'role' => 'worker',
    ]);

    // Log them in
    Auth::login($user);

    // Auto-create a blank worker record
    \App\Models\Worker::firstOrCreate(['user_id' => $user->id]);

    // Redirect them to onboarding wizard
    return redirect()->route('profile.onboarding');
}
public function login(Request $request)
{
    $credentials = $request->only('email', 'password');

    if (Auth::attempt($credentials)) {
        $user = Auth::user();

        // If the user is a worker and profile incomplete, redirect to onboarding
        if ($user->role === 'worker') {
            $worker = \App\Models\Worker::where('user_id', $user->id)->first();
            if (!$worker || empty($worker->skills) || empty($worker->location)) {
                return redirect()->route('profile.onboarding');
            }
        }

        // Otherwise, redirect to correct dashboard
        return $user->role === 'client'
            ? redirect()->route('client.dashboard')
            : redirect()->route('worker.dashboard');
    }

    return back()->withErrors(['email' => 'Invalid credentials.']);
}

}
