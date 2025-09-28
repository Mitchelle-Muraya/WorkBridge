<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    /**
     * Show the registration form.
     */
    public function create() // 👈 renamed from showRegistrationForm to match your route
    {
        return view('auth.register'); // make sure this blade exists
    }

    /**
     * Handle user registration.
     */
    public function store(Request $request) // 👈 renamed from register to store
    {
        $request->validate([
            'name'     => 'required|string|max:191',
            'email'    => 'required|string|email|max:191|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'neutral', // default until they choose
        ]);

        Auth::login($user);

        return redirect()->route('choose.role');
    }
}
