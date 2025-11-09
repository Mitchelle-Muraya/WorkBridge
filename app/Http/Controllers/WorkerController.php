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

        // 🧮 Profile completion percentage
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

        // 📊 Dashboard Statistics
        $availableJobsCount = Job::count();
        $pendingApplicationsCount = Application::where('user_id', $userId)->count();
        $unreadMessages = Message::where('receiver_id', $userId)->count();
        $averageRating = Review::where('worker_id', $userId)->avg('rating');

        // 💡 AI-powered Recommended Jobs from Flask API
        $recommendedJobs = collect(); // default empty
        if ($worker && !empty($worker->skills)) {
            try {
                // ✅ Send the worker's skills to your Render Flask API
                $response = Http::timeout(10)->post('https://workbridge-bc3m.onrender.com/predict_job', [
                    'description' => $worker->skills,
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $recommendedWorkers = $data['recommended_workers'] ?? [];

                    // ✅ Match recommended worker names to jobs in your DB
                    if (!empty($recommendedWorkers)) {
                        $recommendedJobs = Job::where(function ($q) use ($recommendedWorkers) {
                            foreach ($recommendedWorkers as $rec) {
                                $q->orWhere('title', 'like', "%{$rec}%")
                                  ->orWhere('description', 'like', "%{$rec}%");
                            }
                        })->take(5)->get();
                    }
                }
            } catch (\Exception $e) {
                // Flask API may be offline
                $recommendedJobs = collect();
            }
        }

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
     * 📋 Display all jobs the worker has applied for
     */
    public function appliedJobs()
    {
        $applications = Application::with('job')
            ->where('user_id', Auth::id())
            ->get();

        return view('worker.applied-jobs', compact('applications'));
    }

    /**
     * ✉️ Display worker applications page
     */
    public function applications()
    {
        $applications = Application::with('job')
            ->where('user_id', Auth::id())
            ->get();

        return view('worker.applications', compact('applications'));
    }

    /**
     * 👤 Worker profile page
     */
    public function profile()
    {
        return view('worker.profile');
    }

    /**
     * ⭐ Worker ratings page
     */
    public function ratings()
    {
        return view('worker.ratings');
    }

    /**
     * ⚙️ Worker settings page
     */
    public function settings()
    {
        return view('worker.settings');
    }

    /**
     * 🔍 AJAX search for jobs
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
     * 💾 Save or update worker profile
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
            $photoPath = $request->file('photo')->store('profiles', 'public');
            $worker->photo = $photoPath;
        }

        if ($request->hasFile('resume')) {
            $resumePath = $request->file('resume')->store('resumes', 'public');
            $worker->resume = $resumePath;
        }

        $worker->save();

        return redirect()
            ->route('worker.profile')
            ->with('success', 'Profile saved successfully!');
    }

    /**
     * 🔎 Find Jobs page
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
            ->get();

        return view('worker.find-jobs', compact('jobs'));
    }
}
