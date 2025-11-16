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

        // 🧮 Profile Completion
        $requiredFields = ['skills', 'experience', 'photo'];
        $filled = 0;
        if ($worker) {
            foreach ($requiredFields as $field) {
                if (!empty($worker->$field)) $filled++;
            }
        }
        $profilePercentage = count($requiredFields) > 0 ? round(($filled / count($requiredFields)) * 100) : 0;
        $profileIncomplete = $profilePercentage < 100;

        // 📊 Dashboard Stats
        $availableJobsCount = Job::count();
        $pendingApplicationsCount = Application::where('user_id', $userId)->count();
        $unreadMessages = Message::where('receiver_id', $userId)->count();
        $averageRating = Review::where('worker_id', $userId)->avg('rating');

        // 💡 AI Recommended Jobs
        $recommendedJobs = collect();
        if ($worker && !empty($worker->skills)) {
            try {
                $response = Http::post('https://workbridge-bc3m.onrender.com/predict_job', [
                    'description' => $worker->skills
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $apiJobs = $data['recommended_jobs'] ?? [];

                    if (!empty($apiJobs)) {
                        $recommendedJobs = Job::where(function ($q) use ($apiJobs) {
                            foreach ($apiJobs as $job) {
                                $q->orWhere('title', 'like', "%{$job['job_title']}%")
                                  ->orWhere('description', 'like', "%{$job['job_title']}%");
                            }
                        })->take(5)->get();
                    }
                }
            } catch (\Exception $e) {
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

    // Other methods unchanged
    public function appliedJobs() {
        $applications = Application::with('job')->where('user_id', Auth::id())->get();
        return view('worker.applied-jobs', compact('applications'));
    }

    public function applications() {
        $applications = Application::with('job')->where('user_id', Auth::id())->get();
        return view('worker.applications', compact('applications'));
    }

    public function profile() { return view('worker.profile'); }
    public function ratings() { return view('worker.ratings'); }
    public function settings() { return view('worker.settings'); }

    public function searchJobs(Request $request) {
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

    public function saveProfile(Request $request) {
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
        return redirect()->route('worker.profile')->with('success', 'Profile saved successfully!');
    }

    public function findJobs(Request $request) {
        $query = $request->input('query');
        $jobs = Job::where('status', 'pending')
            ->when($query, function($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%")
                  ->orWhere('location', 'like', "%{$query}%");
            })
            ->get();
        return view('worker.find-jobs', compact('jobs'));
    }
}
