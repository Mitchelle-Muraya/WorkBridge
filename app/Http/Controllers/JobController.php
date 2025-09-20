<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Job;
use Illuminate\Http\Request;

class JobController extends Controller
{
    // Landing page
    public function landing()
    {
        if (Auth::check()) {
            $worker = Auth::user();

            // Simple matching (later replace with ML)
            $recommendedJobs = Job::where('title', 'LIKE', '%' . $worker->skills . '%')
                ->orWhere('description', 'LIKE', '%' . $worker->skills . '%')
                ->take(10)
                ->get();

            return view('landing', compact('recommendedJobs'));
        }

        $jobs = Job::latest()->take(10)->get();
        return view('landing', compact('jobs'));
    }

    // Job details
    public function show($id)
    {
        $job = Job::findOrFail($id);

        if (!Auth::check()) {
            return redirect()->route('register')->with('info', 'Please sign up to apply.');
        }

        return view('jobs.show', compact('job'));
    }

    // Apply for a job
    public function apply(Request $request, $id)
    {
        $job = Job::findOrFail($id);
        $worker = Auth::user();

        $job->applications()->create([
            'worker_id' => $worker->id,
            'cover_letter' => $request->cover_letter,
        ]);

        return back()->with('success', 'Application submitted!');
    }
}
