<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase\Factory;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class FirebaseAuthController extends Controller
{
    protected $auth;

    public function __construct()
    {
        // Connect to Firebase with your credentials
        $this->auth = (new Factory)
            ->withServiceAccount(base_path('firebase_credentials.json'))
            ->createAuth();
    }

    /**
     * Verify Firebase token from frontend and log the user in.
     */
    public function verifyToken(Request $request)
    {
        try {
            // Get token from frontend
            $idToken = $request->input('token');
            if (!$idToken) {
                return response()->json(['error' => 'Missing token'], 400);
            }

            // Verify Firebase token
            $verifiedIdToken = $this->auth->verifyIdToken($idToken);
            $uid = $verifiedIdToken->claims()->get('sub');
            $firebaseUser = $this->auth->getUser($uid);

            // Sync with local database
            $user = User::updateOrCreate(
                ['email' => $firebaseUser->email],
                [
                    'name' => $firebaseUser->displayName ?? 'Unnamed User',
                    'email_verified_at' => now(),
                    'password' => bcrypt(str()->random(16)), // random password placeholder
                ]
            );

            // Log user into Laravel
            Auth::login($user);

            return response()->json([
                'message' => 'User authenticated successfully',
                'user' => $user
            ]);

        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 401);
        }
    }
}
