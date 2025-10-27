<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Job;
use App\Models\Application;
use App\Models\Message;
use App\Models\Review;
use App\Models\Worker;
use Illuminate\Support\Facades\Http;

class WorkerController extends Controller
{
    /**
     * Display the Worker Dashboard
     */
    public function index(Request $request)
{
    $userId = Auth::id();
    $worker = Worker::where('user_id', $userId)->first();

    // 🧮 Compute Profile Completion
    $requiredFields = ['skills', 'experience', 'photo', ];
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

    // 📊 Dashboard Data
    $availableJobsCount = Job::count();
    $pendingApplicationsCount = Application::where('user_id', $userId)->count();
    $unreadMessages = Message::where('receiver_id', $userId)->count();
    $averageRating = Review::where('worker_id', $userId)->avg('rating');

    // 💡 Recommended Jobs (based on worker skills)
    if ($worker && !empty($worker->skills)) {
        $skills = array_filter(array_map('trim', explode(',', $worker->skills)));

        $recommendedJobs = Job::where('status', 'pending')
            ->where(function ($q) use ($skills) {
                foreach ($skills as $skill) {
                    $q->orWhere('title', 'like', "%{$skill}%")
                      ->orWhere('description', 'like', "%{$skill}%");
                }
            })
            ->limit(5)
            ->get();
    } else {
        $recommendedJobs = collect(); // fallback to empty collection
    }

    // ✅ Pass everything to the view
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
     $user = auth()->user();
    $worker = \App\Models\Worker::where('user_id', $user->id)->first();

    $recommendedJobs = collect();

    if ($worker && $worker->skills) {
        try {
            $response = Http::post('http://127.0.0.1:5000/predict_job', [
                'description' => $worker->skills
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $recommendedWorkers = $data['recommended_workers'] ?? [];

                // Use those worker names to match jobs from your DB
                $recommendedJobs = \App\Models\Job::where(function ($q) use ($recommendedWorkers) {
                    foreach ($recommendedWorkers as $rec) {
                        $q->orWhere('title', 'like', "%{$rec}%")
                          ->orWhere('description', 'like', "%{$rec}%");
                    }
                })->take(5)->get();
            }
        } catch (\Exception $e) {
            // fallback — Flask API might be offline
            $recommendedJobs = collect();
        }
    }

    return view('dashboard.worker', compact('worker', 'recommendedJobs'));
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
     * Display the worker applications page
     */
    public function applications()
    {
        $applications = Application::with('job')
            ->where('user_id', Auth::id())
            ->get();

        return view('worker.applications', compact('applications'));
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

    /**
     * Display the worker settings page
     */
    public function settings()
    {
        return view('worker.settings');
    }

    /**
     * AJAX autocomplete search for job titles
     */
    public function searchJobs(Request $request)
{
    $term = $request->get('term');

    $jobs = Job::query()
        ->when($term, function($q) use ($term) {
            $q->where('title', 'like', "%{$term}%")
              ->orWhere('description', 'like', "%{$term}%")
              ->orWhere('location', 'like', "%{$term}%");
        })
        ->orderBy('created_at', 'desc')
        ->get();

    return view('partials.search-results', compact('jobs'))->render();
}




public function saveProfile(Request $request)
{
    $userId = Auth::id();

    // ✅ Validate inputs
    $request->validate([
        'skills' => 'required|string|max:255',
        'experience' => 'required|string|max:255',
        'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        'resume' => 'nullable|mimes:pdf,doc,docx|max:5120', // optional CV upload
    ]);

    // ✅ Find or create the worker profile
    $worker = Worker::firstOrNew(['user_id' => $userId]);
    $worker->skills = $request->skills;
    $worker->experience = $request->experience;

    // ✅ Handle profile photo upload
    if ($request->hasFile('photo')) {
        $photoPath = $request->file('photo')->store('profiles', 'public');
        $worker->photo = $photoPath;
    }

    // ✅ Handle resume upload (optional)
    if ($request->hasFile('resume')) {
        $resumePath = $request->file('resume')->store('resumes', 'public');
        $worker->resume = $resumePath;
    }

    // ✅ Save worker info
    $worker->save();

    return redirect()
        ->route('worker.profile')
        ->with('success', 'Profile saved successfully!');
}
public function findJobs(Request $request)
{
    $query = $request->input('query');

    $jobs = Job::where('status', 'pending') // or remove this line if you want all jobs
        ->when($query, function($q) use ($query) {
            $q->where('title', 'like', "%{$query}%")
              ->orWhere('description', 'like', "%{$query}%")
              ->orWhere('location', 'like', "%{$query}%");
        })
        ->get();

    return view('worker.find-jobs', compact('jobs'));
}



}
