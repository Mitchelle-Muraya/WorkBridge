<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use App\Models\Job;
use App\Models\Application;

class ClientController extends Controller
{
    // CLIENT DASHBOARD
   public function index()
{
    $clientId = Auth::id();

    $totalJobs = Job::where('client_id', $clientId)->count();
    $jobsInProgress = Job::where('client_id', $clientId)->where('status', 'in_progress')->count();
    $completedJobs = Job::where('client_id', $clientId)->where('status', 'completed')->count();

    $applications = Application::whereHas('job', function ($q) use ($clientId) {
        $q->where('client_id', $clientId);
    })->with(['user', 'job'])->latest()->take(5)->get();

    return view('dashboard.client', compact('totalJobs', 'jobsInProgress', 'completedJobs', 'applications'));
}

    public function createJob() {
        return view('client.post-job');
    }

    public function storeJob(Request $request)
{
    $user = Auth::user();

    // ✅ Ensure client record exists in `clients` table
    $client = \App\Models\Client::firstOrCreate(
        ['user_id' => $user->id],
        ['company' => $user->name . ' Company', 'photo' => null]
    );

    // ✅ Update user mode to client
    if ($user->mode !== 'client') {
        $user->update(['mode' => 'client']);
    }

    // ✅ Validate and save the job
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'required|string',
        'skills_required' => 'required|string',
        'budget' => 'required|numeric|min:0',
        'deadline' => 'required|date|after:today',
    ]);

    $validated['client_id'] = $client->id;

    \App\Models\Job::create($validated);

    return redirect()->route('client.my-jobs')->with('success', 'Job posted successfully!');
}

    public function myJobs() {
        $jobs = Job::where('client_id', Auth::id())->latest()->get();
        return view('client.my-jobs', compact('jobs'));
    }

    public function messages() { return view('messages.index'); }
    public function reviews() { return view('client.reviews'); }

    public function viewApplications() {
        $clientId = Auth::id();
        $applications = Application::whereHas('job', fn($q) => $q->where('client_id', $clientId))
            ->with(['user', 'job'])
            ->latest()
            ->get();
        return view('client.applications', compact('applications'));
    }

    public function applications() {
        return $this->viewApplications();
    }
}
