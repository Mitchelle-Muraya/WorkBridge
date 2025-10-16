<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Job;

class LandingController extends Controller
{
    public function index()
    {
        $message = ''; // ✅ Initialize to avoid "undefined variable" errors
        $jobs = [];    // ✅ Initialize to handle cases with no jobs

        // ✅ If user is logged in
        if (Auth::check()) {
            $user = Auth::user();

            if ($user->role === 'worker') {
                $message = "Welcome back, {$user->name}! Here are jobs matching your skills 👷‍♀️";
                $jobs = Job::where('status', 'open')
                    ->latest()
                    ->take(6)
                    ->get();
            } elseif ($user->role === 'client') {
                $message = "Welcome back, {$user->name}! Ready to hire today? 👩‍💼";
                $jobs = Job::where('user_id', $user->id)
                    ->latest()
                    ->get();
            } elseif ($user->role === 'admin') {
                $message = "Hello Admin, {$user->name}! Manage your platform here.";
                $jobs = []; // Admin can view system metrics instead
            }
        }
        // ✅ If no one is logged in
        else {
            $message = "Hire skilled workers or find jobs that match your skills — all in one place.";
            $jobs = Job::latest()->take(6)->get();
        }

        // ✅ Return view safely with defined variables
        return view('landing', compact('jobs', 'message'));
    }
}
