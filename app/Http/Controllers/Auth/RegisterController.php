<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class RegisterController extends Controller
{
    /**
     * Show registration form
     */
    public function create()
    {
        return view('auth.register'); // no argument expected here
    }

    /**
     * Handle registration POST
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // ✅ Create user (default role = worker)
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'worker', // default role
        ]);

        // ✅ Log the user in automatically
        auth()->login($user);

        return redirect()->route('worker.dashboard')->with('success', 'Welcome to WorkBridge!');
    }
}
