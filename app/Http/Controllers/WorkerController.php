<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Models\Job;
use App\Models\Application;
use App\Models\Message;
use App\Models\Review;
use App\Models\Worker;

class WorkerController extends Controller
{
    /**
     * 🧩 Display the Worker Dashboard
     */
    public function index(Request $request)
    {
        $userId = Auth::id();
        $worker = Worker::where('user_id', $userId)->first();

        /**
         * 🧮 PROFILE COMPLETION CALCULATION
         */
        $requiredFields = ['skills', 'experience', 'photo'];
        $filled = 0;

        if ($worker) {
            foreach ($requiredFields as $field) {
                if (!empty($worker->$field)) {
                    $filled++;
                }
            }
        }

        $profilePercentage = count($requiredFields) > 0
            ? round(($filled / count($requiredFields)) * 100)
            : 0;

        $profileIncomplete = $profilePercentage < 100;

        /**
         * 📊 DASHBOARD STATS
         */
        $availableJobsCount = Job::count();

        $pendingApplicationsCount = Application::where('user_id', $userId)->count();

        // ✅ FIXED unread messages count
        $unreadMessages = Message::where('receiver_id', $userId)
            ->where('is_read', 0)
            ->count();

        $averageRating = Review::where('worker_id', $userId)->avg('rating');

        /**
         * 🌟 SMART JOB RECOMMENDATION (Skill-Based)
         */
        $recommendedJobs = collect();

        if ($worker && !empty($worker->skills)) {
            // Split skills by comma and clean
            $skills = array_map('trim', explode(',', strtolower($worker->skills)));

            // Search for jobs that match any skill in description or required skills
            $recommendedJobs = Job::where(function ($query) use ($skills) {
                    foreach ($skills as $skill) {
                        $query->orWhere('skills_required', 'LIKE', "%{$skill}%")
                              ->orWhere('description', 'LIKE', "%{$skill}%");
                    }
                })
                ->where('status', 'pending')
                ->latest()
                ->take(6)
                ->get();
        }

        /**
         * 🔁 Return Dashboard View
         */
        return view('dashboard.worker', compact(
            'worker',
            'profileIncomplete',
            'profilePercentage',
            'availableJobsCount',
            'pendingApplicationsCount',
            'unreadMessages',
            'averageRating',
            'recommendedJobs'
        ));
    }

    /**
     * 💼 View Applied Jobs
     */
    public function appliedJobs()
    {
        $applications = Application::with('job')
            ->where('user_id', Auth::id())
            ->get();

        return view('worker.applied-jobs', compact('applications'));
    }

    /**
     * 📄 View Applications
     */
    public function applications()
    {
        $applications = Application::with('job')
            ->where('user_id', Auth::id())
            ->get();

        return view('worker.applications', compact('applications'));
    }

    /**
     * 👤 Worker Profile Page
     */
    public function profile()
    {
        return view('worker.profile');
    }

    /**
     * ⭐ Worker Ratings Page
     */
    public function ratings()
    {
        return view('worker.ratings');
    }

    /**
     * ⚙️ Worker Settings Page
     */
    public function settings()
    {
        return view('worker.settings');
    }

    /**
     * 🔍 Job Search (AJAX)
     */
    public function searchJobs(Request $request)
    {
        $term = $request->get('term');

        $jobs = Job::query()
            ->when($term, function ($q) use ($term) {
                $q->where('title', 'like', "%{$term}%")
                  ->orWhere('description', 'like', "%{$term}%")
                  ->orWhere('location', 'like', "%{$term}%");
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('partials.search-results', compact('jobs'))->render();
    }

    /**
     * 💾 Save Worker Profile
     */
    public function saveProfile(Request $request)
    {
        $userId = Auth::id();

        $request->validate([
            'skills' => 'required|string|max:255',
            'experience' => 'required|string|max:255',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'resume' => 'nullable|mimes:pdf,doc,docx|max:5120',
        ]);

        $worker = Worker::firstOrNew(['user_id' => $userId]);
        $worker->skills = $request->skills;
        $worker->experience = $request->experience;

        if ($request->hasFile('photo')) {
            $worker->photo = $request->file('photo')->store('profiles', 'public');
        }

        if ($request->hasFile('resume')) {
            $worker->resume = $request->file('resume')->store('resumes', 'public');
        }

        $worker->save();

        return redirect()
            ->route('worker.profile')
            ->with('success', 'Profile saved successfully!');
    }

    /**
     * 🔎 Find Jobs (Normal Page)
     */
    public function findJobs(Request $request)
    {
        $query = $request->input('query');

        $jobs = Job::where('status', 'pending')
            ->when($query, function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%")
                  ->orWhere('location', 'like', "%{$query}%");
            })
            ->latest()
            ->get();

      return view('worker.find-jobs', compact('jobs'));
        }
        public function showJob($id)
{
    $job = Job::findOrFail($id);
    return view('worker.job-details', compact('job'));
}

public function reviews()
{
    $workerId = auth()->id();

    // Fetch all reviews related to this worker
    $reviews = \App\Models\Review::where('worker_id', $workerId)
        ->with(['client'])
        ->latest()
        ->get();

    return view('worker.reviews', compact('reviews'));
}
public function leaveReview($jobId)
{
    $job = \App\Models\Job::findOrFail($jobId);
    $client = \App\Models\User::findOrFail($job->client_id);

    return view('worker.leave-review', compact('job', 'client'));
}


public function submitReview(Request $request)
{
    $request->validate([
        'job_id' => 'required',
        'client_id' => 'required',
        'rating' => 'required|integer|min:1|max:5',
        'review' => 'required|string'
    ]);

    \App\Models\Review::create([
        'job_id' => $request->job_id,
        'client_id' => $request->client_id,
        'worker_id' => Auth::id(),
        'rating' => $request->rating,
        'review' => $request->review,
    ]);

    return redirect()->route('worker.dashboard')->with('success', 'Review submitted successfully!');
}

public function reviewClient($jobId)
{
    $job = Job::with('client.user')->findOrFail($jobId);

    // The client who posted the job
    $client = $job->client->user;

    return view('worker.review-client', compact('job', 'client'));
}

}
