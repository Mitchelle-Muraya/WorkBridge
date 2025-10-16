<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;  // ✅ Missing
use App\Models\Job;
use App\Models\Application;
use App\Models\Review;
use App\Models\Client;   // ✅ Missing
use App\Models\Worker;   // ✅ Missing


class DashboardController extends Controller
{
    /**
     * 🧑‍💼 Client Dashboard
     * Displays jobs posted by the client, with status statistics.
     */
    public function clientDashboard()
    {
        $userId = auth()->id();

        // All jobs posted by this client
        $jobs = Job::where('user_id', $userId)->latest()->get();

        // Summary counts
        $totalJobs = $jobs->count();
        $inProgress = $jobs->where('status', 'in_progress')->count();
        $completed = $jobs->where('status', 'completed')->count();

        return view('dashboard.client', compact('jobs', 'totalJobs', 'inProgress', 'completed'));
    }

    /**
     * 👷 Worker Dashboard
     * Displays available, recommended, and applied jobs + worker reviews.
     */



    // ... the rest of your dashboard logic ...


    public function workerDashboard()
    {
        $user = auth()->user();
    $worker = \App\Models\Worker::where('user_id', $user->id)->first();

    if (!$worker || empty($worker->skills) || empty($worker->location)) {
        return redirect()->route('profile.onboarding')->with('info', 'Please complete your profile first.');
    }
        $userId = auth()->id();

        // Jobs the worker has NOT applied for yet
        $availableJobs = Job::whereNotIn('id', function ($query) use ($userId) {
                $query->select('job_id')
                      ->from('applications')
                      ->where('user_id', $userId);
            })
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        // Recommended jobs (for now, same as available — can be ML-enhanced later)
        $recommendedJobs = $availableJobs;

        // Jobs this worker has already applied for
        $appliedJobs = Application::where('user_id', $userId)
            ->with('job')
            ->latest()
            ->get();

        // Reviews left for this worker
        $reviews = Review::where('worker_id', $userId)
            ->latest()
            ->get();

        return view('dashboard.worker', compact(
            'availableJobs',
            'recommendedJobs',
            'appliedJobs',
            'reviews'
        ));
    }

    /**
     * 📝 Post a New Job (Client)
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
            'user_id' => auth()->id(),
            'title' => $request->title,
            'description' => $request->description,
            'location' => $request->location,
            'budget' => $request->budget,
            'status' => 'open',
        ]);

        return back()->with('success', 'Job posted successfully!');
    }

    /**
     * 👀 View Applicants for a Job (Client)
     */
   public function viewApplicants($jobId)
{
    $applications = Application::where('job_id', $jobId)->with('user')->get();
    $job = Job::findOrFail($jobId);
    return view('client.applicants', compact('applications', 'job'));
}

public function acceptApplication($applicationId)
{
    $application = Application::findOrFail($applicationId);

    $application->status = 'accepted';
    $application->save();

    // You can also mark the related job as "in_progress" if desired
    $job = Job::find($application->job_id);
    if ($job) {
        $job->status = 'in_progress';
        $job->save();
    }

    return back()->with('success', '✅ Application accepted successfully!');
}

public function rejectApplication($applicationId)
{
    $application = Application::findOrFail($applicationId);

    $application->status = 'rejected';
    $application->save();

    return back()->with('error', '❌ Application rejected.');
}

    /**
     * ❌ Delete a Job (Client)
     */
    public function deleteJob($jobId)
    {
        Job::findOrFail($jobId)->delete();
        return back()->with('success', 'Job deleted successfully.');
    }

    /**
     * ⭐ Rate a Worker (Client)
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
            'client_id' => auth()->id(),
            'worker_id' => $application->user_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return back()->with('success', 'Review submitted successfully!');
    }
    public function switchMode()
{
    $user = Auth::user();

    if (!$user) {
        return redirect()->route('login')->with('error', 'You must be logged in.');
    }

    if ($user->role === 'worker') {
        // Switch to client
        $user->update(['role' => 'client']);

        Client::firstOrCreate(
            ['user_id' => $user->id],
            ['company_name' => 'N/A', 'contact_number' => 'N/A']
        );

        return redirect()->route('client.dashboard')->with('success', 'You are now a client!');
    }

    // Switch to worker
    $user->update(['role' => 'worker']);

    Worker::firstOrCreate(
        ['user_id' => $user->id],
        ['skills' => '', 'photo' => '', 'resume' => '']
    );

    return redirect()->route('worker.dashboard')->with('success', 'You are now a worker!');

}
}
