<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Job;

class WorkerController extends Controller
{
    // Step 1: Show profile setup form
    public function setupProfile()
    {
        return view('worker.setup-profile');
        // file: resources/views/worker/setup-profile.blade.php
    }

    // Step 2: Save profile details
    public function saveProfile(Request $request)
    {
        $request->validate([
            'skills' => 'required|string',
            'photo' => 'nullable|image|max:2048',
        ]);

        $worker = Auth::user(); // current logged-in worker
        $worker->skills = $request->skills;

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('photos', 'public');
            $worker->photo = $path;
        }

        $worker->save();

        return redirect()->route('landing')
            ->with('success', 'Profile completed! Recommended jobs are now available.');
    }

    // Worker dashboard (after login)
    public function dashboard()
    {
        $worker = Auth::user();
        $recommendedJobs = Job::latest()->take(6)->get(); // replace with ML later

        return view('worker.dashboard', compact('worker', 'recommendedJobs'));
    }

    // Recommended jobs page
    public function recommendedJobs()
    {
        $worker = Auth::user();
        $recommendedJobs = Job::latest()->take(10)->get(); // stub for now

        return view('worker.jobs', compact('worker', 'recommendedJobs'));
    }

    // Resume upload
    public function uploadResume(Request $request)
    {
        $request->validate([
            'resume' => 'required|mimes:pdf,doc,docx|max:2048',
        ]);

        $worker = Auth::user();
        $path = $request->file('resume')->store('resumes', 'public');
        $worker->resume = $path;
        $worker->save();

        return back()->with('success', 'Resume uploaded successfully!');
    }
}
