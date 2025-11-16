<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use App\Models\Job;
use App\Models\Application;
use App\Models\Client;
use App\Models\User; // ✅ so we can query worker users

class ClientController extends Controller
{
    /**
     * 🏠 CLIENT DASHBOARD
     */
    public function index()
    {
        $clientId = Auth::id();

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
     * 💾 STORE A NEW JOB + GET ML RECOMMENDATIONS
     */
    public function storeJob(Request $request)
    {
        $user = Auth::user();

        // ✅ Ensure client record exists
        $client = Client::firstOrCreate(
            ['user_id' => $user->id],
            ['company' => $user->name . ' Company', 'photo' => null]
        );

        // ✅ Switch mode if needed
        if ($user->mode !== 'client') {
            $user->update(['mode' => 'client']);
        }

        // ✅ Validate input
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'skills_required' => 'required|string',
            'budget' => 'required|numeric|min:0',
            'deadline' => 'required|date|after:today',
        ]);

        $validated['client_id'] = $client->id;

        // ✅ Save job
        $job = Job::create($validated);

        /**
         * 🔮 MACHINE LEARNING INTEGRATION
         * Send job description to ML API → get predicted category
         */
        $predictedCategory = null;
        try {
            $response = Http::post('https://workbridge-bc3m.onrender.com/predict_job', [
                'description' => $validated['description'],
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $predictedCategory = $data['predicted_category'] ?? null;
            }
        } catch (\Exception $e) {
            \Log::error('ML API error: ' . $e->getMessage());
        }

        /**
         * 🎯 FIND MATCHING WORKERS BASED ON SKILLS
         * Compare the “skills_required” field with each worker’s skills.
         */
        $requiredSkills = array_map('trim', explode(',', strtolower($validated['skills_required'])));

        $recommendedWorkers = User::where('mode', 'worker')
            ->where(function ($query) use ($requiredSkills) {
                foreach ($requiredSkills as $skill) {
                    $query->orWhere('skills', 'LIKE', "%{$skill}%");
                }
            })
            ->take(5)
            ->pluck('name')
            ->toArray();

        // 💾 Save to session for the dashboard sidebar
        session(['recommended_workers' => $recommendedWorkers]);

        return redirect()
            ->route('client.dashboard')
            ->with('success', 'Job posted successfully!');
    }

    /**
     * 📋 LIST CLIENT JOBS
     */
    public function myJobs()
    {
        $jobs = Job::where('client_id', Auth::id())->latest()->get();
        return view('client.my-jobs', compact('jobs'));
    }

    /**
     * 💬 MESSAGES
     */
    public function messages()
    {
        return view('messages.index');
    }

    /**
     * ⭐ REVIEWS
     */
    public function reviews()
    {
        return view('client.reviews');
    }

    /**
     * 📩 VIEW APPLICATIONS
     */
    public function viewApplications()
    {
        $clientId = Auth::id();
        $applications = Application::whereHas('job', fn($q) => $q->where('client_id', $clientId))
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
