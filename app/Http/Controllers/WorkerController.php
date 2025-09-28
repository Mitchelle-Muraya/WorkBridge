<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Job;
use App\Models\Worker;

class WorkerController extends Controller
{
    // Step 1: Show profile setup form
    public function setupProfile()
    {
        return view('worker.setup-profile');
    }

    // Step 2: Save profile details
    public function saveProfile(Request $request)
    {
        $request->validate([
            'skills' => 'required|string',
            'photo' => 'nullable|image|max:2048',
        ]);

        $user = Auth::user();

        // Either update or create Worker profile linked to users.id
        $worker = Worker::updateOrCreate(
            ['user_id' => $user->id],
            [
                'skills' => $request->skills,
                'photo' => $request->hasFile('photo')
                    ? $request->file('photo')->store('photos', 'public')
                    : null,
            ]
        );

        return redirect()->route('landing')
            ->with('success', 'Profile completed! Recommended jobs are now available.');
    }

    // Worker dashboard
    public function dashboard()
    {
        $user = Auth::user();
        $worker = Worker::where('user_id', $user->id)->first();

        $recommendedJobs = Job::latest()->take(6)->get(); // replace with ML later

        return view('worker.dashboard', compact('worker', 'recommendedJobs'));
    }

    // Recommended jobs page
    public function recommendedJobs()
    {
        $recommendedJobs = Job::latest()->take(10)->get();

        return view('worker.jobs', compact('recommendedJobs'));
    }

    // Resume upload
    public function uploadResume(Request $request)
    {
        $request->validate([
            'resume' => 'required|mimes:pdf,doc,docx|max:2048',
        ]);

        $user = Auth::user();
        $worker = Worker::where('user_id', $user->id)->first();

        if ($worker) {
            $path = $request->file('resume')->store('resumes', 'public');
            $worker->resume = $path;
            $worker->save();
        }

        return back()->with('success', 'Resume uploaded successfully!');
    }
}
