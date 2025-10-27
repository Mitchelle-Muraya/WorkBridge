<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Job;
use Illuminate\Support\Facades\Auth;
use App\Models\Application;

class ClientController extends Controller
{
    // CLIENT DASHBOARD
    public function index()
    {
        $clientId = Auth::id();

        // ✅ Job stats for this client
        $totalJobs = Job::where('client_id', $clientId)->count();
        $jobsInProgress = Job::where('client_id', $clientId)
            ->where('status', 'in_progress')
            ->count();
        $completedJobs = Job::where('client_id', $clientId)
            ->where('status', 'completed')
            ->count();

        // ✅ Recent applications for this client's jobs
        $applications = Application::whereHas('job', function ($query) use ($clientId) {
                $query->where('client_id', $clientId);
            })
            ->with(['user', 'job'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // ✅ (Optional) Log debug info instead of dd()
        logger()->info('Client dashboard loaded', [
            'client_id' => $clientId,
            'totalJobs' => $totalJobs,
            'inProgress' => $jobsInProgress,
            'completed' => $completedJobs,
            'application_count' => $applications->count(),
        ]);


        // ✅ Return dashboard view
        return view('dashboard.client', compact(
            'totalJobs',
            'jobsInProgress',
            'completedJobs',
            'applications'
        ));
    }

    // POST JOB PAGE
    public function createJob()
    {
        return view('client.post-job');
    }

    // STORE JOB
    public function storeJob(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'budget' => 'required|numeric',
            'deadline' => 'required|date',
        ]);

        $clientId = Auth::id();

        $job = new Job();
        $job->title = $request->title;
        $job->description = $request->description;
        $job->budget = $request->budget;
        $job->deadline = $request->deadline;
        $job->client_id = $clientId;
        $job->status = 'pending';

        $skills = $request->skills_required ?? [];
        if (!empty($request->other_skill)) {
            $skills[] = $request->other_skill;
        }
        $job->skills_required = implode(', ', $skills);
        $job->location = $request->location ?? 'Not specified';
        $job->save();

        return redirect()->back()->with('success', 'Job posted successfully!');
    }

    // MY JOBS PAGE
    public function myJobs()
    {
        $jobs = Job::where('client_id', Auth::id())->latest()->get();
        return view('client.my-jobs', compact('jobs'));
    }

    // MESSAGES & REVIEWS
   public function messages() {
    return view('messages.index');
}


    public function reviews()
    {
        return view('client.reviews');
    }

    // APPLICATIONS
    public function viewApplications()
    {
        $clientId = Auth::id();

        $applications = Application::whereHas('job', function ($query) use ($clientId) {
                $query->where('client_id', $clientId);
            })
            ->with(['user', 'job'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('client.applications', compact('applications'));
    }

    public function applications()
    {
        return $this->viewApplications();
    }
}
