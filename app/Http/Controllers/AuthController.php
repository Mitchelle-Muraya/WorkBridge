<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;




class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login'); // create resources/views/auth/login.blade.php
    }

   public function showRegisterForm()
{
    return view('auth.register'); // create this view
}

public function register(Request $request)
{
    // validate input
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:workers,email',
        'password' => 'required|min:6|confirmed',
    ]);

    // create worker by default
    $worker = \App\Models\Worker::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => bcrypt($request->password),
        'role' => 'worker',
    ]);

    // log in the user
    Auth::login($worker);

    return redirect('/worker/dashboard');
}

}
