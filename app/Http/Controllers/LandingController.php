<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Job;

class LandingController extends Controller
{
    public function index()
    {
        if (Auth::check()) {
            // Show recommendations if logged in
            $recommendedJobs = Job::latest()->take(6)->get();
            // TODO: Replace with ML-based logic
            return view('landing', compact('recommendedJobs'));
        } else {
            // Show latest jobs to guests
            $jobs = Job::latest()->take(6)->get();
            return view('landing', compact('jobs'));
        }
    }
}
