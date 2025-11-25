<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Job;
use App\Models\Application;
use App\Models\Message;
use App\Models\Review;
use App\Models\Worker;

class WorkerController extends Controller
{
    /**
     * 🧩 Display Worker Dashboard
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

        $profilePercentage = count($requiredFields)
            ? round(($filled / count($requiredFields)) * 100)
            : 0;

        $profileIncomplete = $profilePercentage < 100;

        /**
         * 🌟 SMART JOB RECOMMENDATION ENGINE
         */
        $recommendedJobs = collect();

        if ($worker && !empty($worker->skills)) {

            $skills = array_map('trim', explode(',', strtolower($worker->skills)));

            $recommendedJobs = Job::where('status', 'approved')
                ->where(function ($query) use ($skills) {
                    foreach ($skills as $skill) {
                        $query->orWhere('skills_required', 'LIKE', "%{$skill}%")
                              ->orWhere('description', 'LIKE', "%{$skill}%");
                    }
                })
                ->latest()
                ->take(6)
                ->get();
        }

        // 🔥 FINAL FIX: Only count recommended jobs
        $availableJobsCount = $recommendedJobs->count();

        /**
         * 📊 DASHBOARD METRICS
         */
        $pendingApplicationsCount = Application::where('user_id', $userId)->count();

        $unreadMessages = Message::where('receiver_id', $userId)
            ->where('is_read', 0)
            ->count();

        $averageRating = Review::where('worker_id', $userId)->avg('rating');

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
     * ⭐ Worker Ratings
     */
    public function ratings()
    {
        return view('worker.ratings');
    }

    /**
     * ⚙️ Worker Settings
     */
    public function settings()
    {
        return view('worker.settings');
    }

    /**
     * 🔍 AJAX Job Search
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
            ->latest()
            ->get();

        return view('partials.search-results', compact('jobs'))->render();
    }

    /**
     * 💾 Save Worker Profile
     */
    public function saveProfile(Request $request)
{
    $request->validate([
        'skills' => 'required',
        'experience' => 'required',
    ]);

    // Convert Tagify JSON to clean list
    $tagifySkills = json_decode($request->skills, true);
    $cleanSkills  = collect($tagifySkills)->pluck('value')->join(',');

    $worker = Worker::updateOrCreate(
        ['user_id' => Auth::id()],
        [
            'skills'     => $cleanSkills,
            'experience' => $request->experience,
            'availability' => 'available'
        ]
    );

    // PHOTO upload
    if ($request->hasFile('photo')) {
        $path = $request->file('photo')->store('profiles', 'public');
        $worker->photo = $path;
        $worker->save();
    }

    // RESUME upload
    if ($request->hasFile('resume')) {
        $resumePath = $request->file('resume')->store('resumes', 'public');
        $worker->resume = $resumePath;
        $worker->save();
    }

    Auth::user()->update(['is_profile_complete' => 1]);

    return redirect()->route('worker.dashboard')
                     ->with('success', 'Profile updated successfully!');
}

    /**
     * 🔎 Find Jobs Page
     */
    public function findJobs(Request $request)
    {
        $query = $request->input('query');

        $jobs = Job::where('status', 'approved')
            ->when($query, function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%")
                  ->orWhere('location', 'like', "%{$query}%");
            })
            ->latest()
            ->get();

        return view('worker.find-jobs', compact('jobs'));
    }

    /**
     * 📄 Show Job
     */
    public function showJob($id)
    {
        $job = Job::findOrFail($id);
        return view('worker.job-details', compact('job'));
    }

    /**
     * ⭐ Worker Reviews
     */
    public function reviews()
    {
        $workerId = auth()->id();

        $reviews = Review::where('worker_id', $workerId)
            ->with(['client'])
            ->latest()
            ->get();

        return view('worker.reviews', compact('reviews'));
    }

    public function leaveReview($jobId)
    {
        $job = Job::findOrFail($jobId);
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

        Review::create([
            'job_id' => $request->job_id,
            'client_id' => $request->client_id,
            'worker_id' => Auth::id(),
            'rating' => $request->rating,
            'review' => $request->review,
        ]);

        return redirect()->route('worker.dashboard')
            ->with('success', 'Review submitted successfully!');
    }

    public function reviewClient($jobId)
    {
        $job = Job::with('client.user')->findOrFail($jobId);
        $client = $job->client->user;

        return view('worker.review-client', compact('job', 'client'));
    }
    public function editProfile()
{
    $worker = Worker::where('user_id', Auth::id())->first();

    return view('worker.profile', compact('worker'));
}

public function updateProfile(Request $request)
{
    $request->validate([
        'skills' => 'required|string',
        'experience' => 'required|string',
        'photo' => 'nullable|image|mimes:jpg,jpeg,png',
        'resume' => 'nullable|mimes:pdf,doc,docx'
    ]);

    $worker = Worker::updateOrCreate(
        ['user_id' => Auth::id()],
        [
            'skills' => $request->skills,
            'experience' => $request->experience,
        ]
    );

    // PHOTO UPLOAD
    if ($request->hasFile('photo')) {
        $photoPath = $request->file('photo')->store('worker_photos', 'public');
        $worker->photo = $photoPath;
    }

    // RESUME UPLOAD
    if ($request->hasFile('resume')) {
        $resumePath = $request->file('resume')->store('worker_resumes', 'public');
        $worker->resume = $resumePath;
    }

    $worker->save();

    return back()->with('success', 'Profile updated successfully!');
}

public function profile()
{
    $worker = Worker::where('user_id', Auth::id())->first();

    return view('worker.profile', [
        'worker' => $worker
    ]);
}

}
