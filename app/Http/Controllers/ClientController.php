<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Job;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{
    // CLIENT DASHBOARD
    public function index()
{
    $clientId = Auth::id();

    $totalJobs = Job::where('client_id', $clientId)->count();
    $jobsInProgress = Job::where('client_id', $clientId)->where('status', 'in_progress')->count();
    $completedJobs = Job::where('client_id', $clientId)->where('status', 'completed')->count();

    // 👇 Fetch all applications for jobs posted by this client
    $applications = \App\Models\Application::whereHas('job', function ($query) use ($clientId) {
        $query->where('client_id', $clientId);
    })
    ->with(['user', 'job'])
    ->orderBy('created_at', 'desc')
    ->get();

    return view('dashboard.client', compact('totalJobs', 'jobsInProgress', 'completedJobs', 'applications'));
}


    // POST JOB PAGE
    public function createJob()
    {
        return view('client.post-job');
    }

    // STORE JOB
    public function storeJob(Request $request)
{
    // validate data
    $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'required|string',
        'category' => 'required|string|max:255',
        'budget' => 'required|numeric',
        'deadline' => 'required|date',
    ]);

    // get logged in client
    $clientId = Auth::id();

    // create job
    $job = new Job();
    $job->title = $request->title;
    $job->description = $request->description;
    $job->category = $request->category;
    $job->budget = $request->budget;
    $job->deadline = $request->deadline;
    $job->client_id = $clientId;
    $job->status = 'pending'; // default status
    $skills = $request->skills_required ?? [];
if (!empty($request->other_skill)) {
    $skills[] = $request->other_skill;
}
$job->skills_required = implode(', ', $skills);

$job->location = $request->location;


    $job->save();

    return redirect()->back()->with('success', 'Job posted successfully!');
}


    // MY JOBS PAGE
    public function myJobs()
    {
        $jobs = Job::where('client_id', Auth::id())->latest()->get();
        return view('client.my-jobs', compact('jobs'));
    }

    // MESSAGES & REVIEWS (placeholders)
    public function messages() { return view('client.messages'); }
    public function reviews() { return view('client.reviews'); }

public function viewApplications()
{
    $clientId = Auth::id();

    // Fetch all applications for the jobs posted by this client
    $applications = \App\Models\Application::whereHas('job', function ($query) use ($clientId) {
        $query->where('client_id', $clientId);
    })
    ->with(['user', 'job'])
    ->orderBy('created_at', 'desc')
    ->get();

    return view('client.applications', compact('applications'));
}

}
