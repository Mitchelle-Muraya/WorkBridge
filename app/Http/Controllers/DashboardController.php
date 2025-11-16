<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\{Job, Application, Review, Client, Worker, Notification};

class DashboardController extends Controller
{
    /**
     * 🧑‍💼 CLIENT DASHBOARD
     * Shows jobs posted by the client and progress stats.
     */
   public function clientDashboard()
{
    $user = Auth::user();

    $client = Client::where('user_id', $user->id)->first();
    $jobs = Job::where('user_id', $user->id)->latest()->get();

    $totalJobs = $jobs->count();
    $inProgress = $jobs->where('status', 'in_progress')->count();
    $completed = $jobs->where('status', 'completed')->count();

    $applications = Application::whereIn('job_id', $jobs->pluck('id'))
        ->with('user', 'job')
        ->latest()
        ->get();

    // ✅ Fetch AI recommended workers
    $recommended = json_decode($client->recommended_workers ?? '[]', true);

    return view('dashboard.client', compact(
        'jobs', 'applications', 'totalJobs', 'inProgress', 'completed', 'recommended'
    ));
}

    /**
     * 👷 WORKER DASHBOARD
     * Displays available jobs, applications, reviews, and shows profile completion prompt.
     */
    public function workerDashboard()
    {
        $user = Auth::user();

        // Check if worker profile exists
        $worker = Worker::where('user_id', $user->id)->first();
        $hasProfile = $worker ? true : false;

        // Jobs not applied for
        $availableJobs = Job::whereNotIn('id', function ($query) use ($user) {
                $query->select('job_id')->from('applications')->where('user_id', $user->id);
            })
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        // Recommended jobs (same for now — can be ML enhanced later)
        $recommendedJobs = $availableJobs;

        // Jobs already applied for
        $appliedJobs = Application::where('user_id', $user->id)
            ->with('job')
            ->latest()
            ->get();

        // Reviews received
        $reviews = Review::where('worker_id', $user->id)->latest()->get();

        return view('dashboard.worker', compact(
            'availableJobs',
            'recommendedJobs',
            'appliedJobs',
            'reviews',
            'hasProfile'
        ));
    }

    /**
     * 📝 CLIENT POSTS A NEW JOB
     */
    public function postJob(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'budget' => 'required|numeric',
        ]);

        Job::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'description' => $request->description,
            'location' => $request->location,
            'budget' => $request->budget,
            'status' => 'open',
        ]);

        return back()->with('success', '✅ Job posted successfully!');
    }

    /**
     * 👀 VIEW JOB APPLICANTS
     */
    public function viewApplicants($jobId)
    {
        $job = Job::findOrFail($jobId);
        $applications = Application::where('job_id', $jobId)->with('user')->get();

        return view('client.applicants', compact('applications', 'job'));
    }

    /**
     * ✅ ACCEPT APPLICATION
     */
    public function acceptApplication($applicationId)
    {
        $application = Application::findOrFail($applicationId);
        $application->status = 'accepted';
        $application->save();

        $job = Job::find($application->job_id);
        if ($job) {
            $job->status = 'in_progress';
            $job->save();
        }

        // Notify worker
        Notification::create([
            'user_id' => $application->user_id,
            'title' => 'Application Accepted',
            'message' => 'Your application for "' . $job->title . '" has been accepted.',
        ]);

        // Notify client (confirmation)
        Notification::create([
            'user_id' => $job->user_id,
            'title' => 'Application Approved',
            'message' => 'You accepted ' . $application->user->name . '\'s application for "' . $job->title . '".',
        ]);

        return back()->with('success', '✅ Application accepted successfully!');
    }

    /**
     * ❌ REJECT APPLICATION
     */
    public function rejectApplication($applicationId)
    {
        $application = Application::findOrFail($applicationId);
        $application->status = 'rejected';
        $application->save();

        $job = Job::find($application->job_id);

        // Notify worker
        Notification::create([
            'user_id' => $application->user_id,
            'title' => 'Application Rejected',
            'message' => 'Your application for "' . $job->title . '" has been rejected.',
        ]);

        return back()->with('error', '❌ Application rejected.');
    }

    /**
     * 🗑️ DELETE A JOB
     */
    public function deleteJob($jobId)
    {
        Job::findOrFail($jobId)->delete();
        return back()->with('success', '🗑️ Job deleted successfully.');
    }

    /**
     * ⭐ CLIENT RATES A WORKER
     */
    public function rateWorker(Request $request, $applicationId)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $application = Application::findOrFail($applicationId);

        Review::create([
            'job_id' => $application->job_id,
            'client_id' => Auth::id(),
            'worker_id' => $application->user_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return back()->with('success', '⭐ Review submitted successfully!');
    }

    /**
     * 🔄 SWITCH ROLE BETWEEN WORKER & CLIENT
     */
    public function switchMode()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login')->with('error', 'You must be logged in.');
        }

        if ($user->role === 'worker') {
            // Switch to Client
            $user->update(['role' => 'client']);
            Client::firstOrCreate(
                ['user_id' => $user->id],
                ['company_name' => 'N/A', 'contact_number' => 'N/A']
            );

            return redirect()->route('client.dashboard')->with('success', '🧑‍💼 You are now a Client!');
        }

        // Switch to Worker
        $user->update(['role' => 'worker']);
        Worker::firstOrCreate(
            ['user_id' => $user->id],
            ['skills' => '', 'photo' => '', 'resume' => '']
        );

        return redirect()->route('worker.dashboard')->with('success', '👷 You are now a Worker!');
    }
}
