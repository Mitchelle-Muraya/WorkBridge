<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Job;
use App\Models\Application;
use Illuminate\Support\Facades\Auth;

class WorkerController extends Controller
{
    /**
     * Display the Worker Dashboard
     */
    public function index(Request $request)
    {
        $query = $request->input('query');
        $worker = Auth::user();

        // ✅ Determine if profile is incomplete (automatic check)
      $profileIncomplete = $worker->profile_status !== 'complete';


        // ✅ Fetch all open jobs (search supported)
        $jobs = Job::where('status', 'pending')
            ->when($query, function ($q) use ($query) {
                $q->where('title', 'like', "%$query%")
                  ->orWhere('category', 'like', "%$query%")
                  ->orWhere('location', 'like', "%$query%");
            })
            ->latest()
            ->get();

        // ✅ Fetch worker's applications with job relationship
        $applications = Application::where('user_id', $worker->id)
            ->with('job')
            ->get();

        // ✅ (NEW) Recommended Jobs — placeholder for ML logic
        // For now, it fetches jobs in same category or location as the worker (mocked logic)
        $recommendedJobs = Job::where('status', 'open')
            ->when($worker->skills, function ($q) use ($worker) {
                $q->where(function ($sub) use ($worker) {
                    $skillsArray = explode(',', $worker->skills);
                    foreach ($skillsArray as $skill) {
                        $sub->orWhere('title', 'like', '%' . trim($skill) . '%')
                            ->orWhere('category', 'like', '%' . trim($skill) . '%');
                    }
                });
            })
            ->when($worker->location, function ($q) use ($worker) {
                $q->orWhere('location', 'like', '%' . $worker->location . '%');
            })
            ->inRandomOrder()
            ->take(3)
            ->get();

        return view('dashboard.worker', compact(
            'jobs',
            'applications',
            'recommendedJobs',
            'profileIncomplete'
        ));
    }

    /**
     * Display all jobs the worker has applied for
     */
    public function appliedJobs()
    {
        $applications = Application::with('job')
            ->where('user_id', Auth::id())
            ->get();

        return view('worker.applied-jobs', compact('applications'));
    }

    /**
     * Display the worker profile page
     */
    public function profile()
    {
        return view('worker.profile');
    }

    /**
     * Display the worker ratings page
     */
    public function ratings()
    {
        return view('worker.ratings');
    }
    public function applications()
{
    $applications = auth()->user()->applications()->with('job')->get();
    return view('worker.applications', compact('applications'));
}

public function settings()
{
    return view('worker.settings');
}

}
