<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Job;
use App\Models\Worker;
use Illuminate\Support\Facades\Auth;

class WorkerController extends Controller
{
    public function dashboard() {
        return view('worker.dashboard', ['worker' => Auth::guard('worker')->user()]);
    }

    public function recommendedJobs() {
        $worker = Auth::guard('worker')->user();
        $worker_skills = $worker->skills; // Or resume_text if using NLP

        // Call Python ML script here to get recommended jobs
        // Example: python3 match_skills.py --worker_id=123

        $jobs = Job::all(); // For now, return all jobs
        return view('worker.jobs', ['jobs' => $jobs]);
    }

    public function uploadResume(Request $request) {
    $request->validate([
        'resume' => 'required|file|mimes:pdf,doc,docx'
    ]);

    $path = $request->file('resume')->store('resumes');

    // ✅ Save to the authenticated worker
    $worker = Auth::guard('worker')->user();
    $worker->resume_path = $path;  // make sure `resume_path` exists in migration
    $worker->save();               // ✅ save the worker object

    return back()->with('success', 'Resume uploaded successfully!');
}

}
