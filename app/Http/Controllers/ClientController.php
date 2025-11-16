<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Job;
use App\Models\Application;
use App\Models\Client;

class ClientController extends Controller
{
    /**
     * 🏠 CLIENT DASHBOARD
     */
   public function index()
{
    $userId = Auth::id();

    // ensure client record always exists
    $client = \App\Models\Client::firstOrCreate(
        ['user_id' => $userId],
        ['company' => Auth::user()->name . ' Company', 'photo' => null]
    );

    $clientId = $client->id;

    $totalJobs = Job::where('client_id', $clientId)->count();
    $jobsInProgress = Job::where('client_id', $clientId)->where('status', 'in_progress')->count();
    $completedJobs = Job::where('client_id', $clientId)->where('status', 'completed')->count();

    $applications = Application::whereHas('job', function ($q) use ($clientId) {
        $q->where('client_id', $clientId);
    })->with(['user', 'job'])->latest()->take(5)->get();

    return view('dashboard.client', compact('totalJobs', 'jobsInProgress', 'completedJobs', 'applications'));
}

    /**
     * 🧱 SHOW JOB CREATION FORM
     */
    public function createJob()
    {
        return view('client.post-job');
    }

    /**
     * 💾 STORE A NEW JOB
     */
  public function storeJob(Request $request)
{
    $user = Auth::user();

    // ✅ Ensure client record exists
    $client = \App\Models\Client::firstOrCreate(
        ['user_id' => $user->id],
        ['company' => $user->name . ' Company', 'photo' => null]
    );

    if ($user->mode !== 'client') {
        $user->update(['mode' => 'client']);
    }

    // ✅ Handle skill array and merge with optional 'other_skill'
    $skills = $request->input('skills_required', []);
    if ($request->filled('other_skill')) {
        $skills[] = $request->input('other_skill');
    }

    $skillsString = implode(', ', $skills); // Convert array to a single string

    // ✅ Validate remaining inputs
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'required|string',
        'budget' => 'required|numeric|min:0',
        'deadline' => 'required|date|after:today',
        'location' => 'required|string|max:255',
    ]);

    // ✅ Add processed fields
    $validated['skills_required'] = $skillsString;
    $validated['client_id'] = $client->id;
    $validated['user_id'] = $user->id;
    $validated['status'] = 'pending';

    // ✅ Save job
    $job = \App\Models\Job::create($validated);

    return redirect()->route('client.my-jobs')->with('success', 'Job posted successfully!');
}

    /**
     * 📋 LIST ALL JOBS POSTED BY THIS CLIENT
     */
    public function myJobs()
    {
        $userId = Auth::id();
        $client = Client::where('user_id', $userId)->first();

        if (!$client) {
            return redirect()->route('dashboard')->with('error', 'Client profile not found.');
        }

        $jobs = Job::where('client_id', $client->id)->latest()->get();
        return view('client.my-jobs', compact('jobs'));
    }

    public function messages()
    {
        return view('messages.index');
    }

    public function reviews()
    {
        return view('client.reviews');
    }

    public function viewApplications()
    {
        $userId = Auth::id();
        $client = Client::where('user_id', $userId)->first();

        if (!$client) {
            return redirect()->route('dashboard')->with('error', 'Client profile not found.');
        }

        $applications = Application::whereHas('job', fn($q) => $q->where('client_id', $client->id))
            ->with(['user', 'job'])
            ->latest()
            ->get();

        return view('client.applications', compact('applications'));
    }

    public function applications()
    {
        return $this->viewApplications();
    }
}
