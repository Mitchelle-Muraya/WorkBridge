<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use App\Models\Worker;
use App\Models\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class GoogleController extends Controller
{
    // Redirect to Google
    public function redirectToGoogle()
    {
        // Save the type of signup: worker or client
        session(['signup_type' => request('type', 'client')]);
        return Socialite::driver('google')->stateless()->redirect();
    }

    // Callback from Google
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            // Log Google user for debugging
            Log::info('Google user data', [
                'name' => $googleUser->name,
                'email' => $googleUser->email,
                'id' => $googleUser->id,
            ]);

            // Check if user already exists
            $user = User::where('email', $googleUser->email)->first();

            if (!$user) {
                // Create new user
                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'avatar' => $googleUser->avatar,
                    'password' => Hash::make(uniqid()), // random password
                    'role' => session('signup_type') ?? 'client',
                ]);

                // Create associated profile table entry
                if ($user->role === 'worker') {
                    Worker::create([
                        'user_id' => $user->id,
                        'skills' => null,
                        'photo' => $googleUser->avatar,
                    ]);
                } else {
                    Client::create([
                        'user_id' => $user->id,
                    ]);
                }
            }

            // ✅ Log in the User model (NOT Worker/Client)
            Auth::login($user);

            // Redirect to dashboard
            return redirect()->route('dashboard');

        } catch (\Exception $e) {
            Log::error('Google login error', ['message' => $e->getMessage()]);
            return redirect()->route('login')->with('error', 'Google login failed.');
        }
    }
}
