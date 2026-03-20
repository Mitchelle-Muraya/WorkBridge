<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Models\Job;
use App\Models\Application;
use App\Models\Client;
use App\Models\SkillRate;

class ClientController extends Controller
{
    /**
     * 🏠 CLIENT DASHBOARD
     */
    public function index()
    {
        $userId = Auth::id();

        // Ensure client profile exists
        $client = Client::firstOrCreate(
            ['user_id' => $userId],
            ['company' => Auth::user()->name . ' Company']
        );

        $clientId = $client->id;

        // Stats
        $totalJobs      = Job::where('client_id', $clientId)->count();
        $jobsInProgress = Job::where('client_id', $clientId)->where('status', 'in_progress')->count();
        $completedJobs  = Job::where('client_id', $clientId)->where('status', 'completed')->count();

        // Applications
        $applications = Application::whereHas('job', fn($q) =>
            $q->where('client_id', $clientId)
        )->with(['user', 'job'])->latest()->get();

        // New application count
        $newApplications = Application::whereHas('job', fn($q) =>
            $q->where('client_id', $clientId)
        )->where('viewed', false)->count();

        /**
         * 🔥 LOAD AI RECOMMENDED WORKERS (SAFELY)
         */
        $workers = [];

        if (!empty($client->recommended_workers)) {
            $decoded = json_decode($client->recommended_workers, true);

            if (is_array($decoded)) {
                $workers = collect($decoded)->map(function ($w) {

                    $name = $w['name'] ?? $w['worker_name'] ?? 'Worker';

                    return [
                        'user_id'    => $w['user_id'] ?? null,
                        'name'       => $name,
                        'skills'     => $w['skills'] ?? '',
                        'location'   => $w['location'] ?? 'Nairobi, Kenya',
                        'experience' => $w['experience'] ?? 0,
                        'rating'     => $w['rating'] ?? rand(3,5),
                        'photo'      => $w['photo'] ??
                            'https://ui-avatars.com/api/?name=' . urlencode($name) .
                            '&background=00b3ff&color=fff'
                    ];
                });
            }
        }

        return view('dashboard.client', compact(
            'totalJobs',
            'jobsInProgress',
            'completedJobs',
            'applications',
            'newApplications',
            'workers'         // IMPORTANT!
        ));
    }

    /**
     * SHOW JOB CREATION FORM
     */
    public function createJob()
    {
        $rates = SkillRate::all();
        return view('client.post-job', compact('rates'));
    }

    /**
     * STORE NEW JOB + AI RECOMMENDATION FETCH
     */
    public function storeJob(Request $request)
    {
        $user = Auth::user();

        $client = Client::firstOrCreate(
            ['user_id' => $user->id],
            ['company' => $user->name . ' Company']
        );

        // Switch to client mode if not already
        if ($user->mode !== 'client') {
            $user->update(['mode' => 'client']);
        }

        // Handle skills
        $skills = $request->input('skills_required', []);
        if ($request->filled('other_skill')) {
            $skills[] = $request->other_skill;
        }

        $skillsString = implode(', ', $skills);

        // Validate input
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'budget' => 'required|numeric|min:0',
            'deadline' => 'required|date|after:today',
            'location' => 'required|string|max:255',
        ]);

        $validated['skills_required'] = $skillsString;
        $validated['client_id'] = $client->id;
        $validated['user_id'] = $user->id;
        $validated['status'] = 'pending';

        $job = Job::create($validated);

        /**
         * 🔥 CALL FLASK RECOMMENDER API
         */
        try {
            $response = Http::post('http://127.0.0.1:10000/recommend_workers', [
                'skills' => $validated['skills_required']
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $recommended = $data['recommended_workers'] ?? [];

                $workers = collect($recommended)->map(function ($w) {
                    $name = $w['worker_name'] ?? $w['name'] ?? 'Worker';

                    return [
                        'user_id'    => $w['id'] ?? null,
                        'name'       => $name,
                        'skills'     => $w['skills'] ?? '',
                        'location'   => $w['location'] ?? 'Nairobi, Kenya',
                        'experience' => $w['experience'] ?? 0,
                        'rating'     => rand(3,5),
                        'photo'      =>
                            'https://ui-avatars.com/api/?name=' . urlencode($name) .
                            '&background=00b3ff&color=fff',
                    ];
                });

                // Avoid self–recommendation
                $workers = $workers->reject(fn($w) => $w['user_id'] == Auth::id())->values();

                $client->recommended_workers = json_encode($workers);
                $client->save();
            }

        } catch (\Exception $e) {
            \Log::error("FLASK RECOMMENDER ERROR: " . $e->getMessage());
        }

        return redirect()->route('client.dashboard')
            ->with('success', 'Job posted successfully! AI recommendations updated.');
    }

    /**
     * LIST CLIENT JOBS
     */
    public function myJobs()
    {
        $client = Client::where('user_id', Auth::id())->firstOrFail();
        $jobs = Job::where('client_id', $client->id)->latest()->get();

        return view('client.my-jobs', compact('jobs'));
    }

    /**
     * EDIT JOB
     */
    public function editJob($id)
    {
        $client = Client::where('user_id', Auth::id())->firstOrFail();
        $job = Job::where('id', $id)->where('client_id', $client->id)->firstOrFail();

        return view('client.edit-job', compact('job'));
    }

    /**
     * UPDATE JOB
     */
    public function updateJob(Request $request, $id)
    {
        $client = Client::where('user_id', Auth::id())->firstOrFail();
        $job = Job::where('id', $id)->where('client_id', $client->id)->firstOrFail();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'budget' => 'required|numeric|min:0',
            'deadline' => 'required|date',
            'location' => 'required|string|max:255',
        ]);

        $job->update($validated);

        return redirect()->route('client.my-jobs')
            ->with('success', 'Job updated successfully.');
    }

    /**
     * DELETE JOB
     */
    public function deleteJob($id)
    {
        $client = Client::where('user_id', Auth::id())->firstOrFail();
        $job = Job::where('id', $id)->where('client_id', $client->id)->firstOrFail();

        $job->delete();

        return redirect()->route('client.my-jobs')
            ->with('success', 'Job deleted successfully.');
    }

    /**
     * VIEW APPLICATIONS
     */
    public function viewApplications()
    {
        $client = Client::where('user_id', Auth::id())->firstOrFail();

        $applications = Application::whereHas('job', fn($q) =>
            $q->where('client_id', $client->id)
        )->with(['user', 'job'])->latest()->get();

        // Mark viewed
        Application::whereHas('job', fn($q) =>
            $q->where('client_id', $client->id)
        )->update(['viewed' => true]);

        return view('client.applications', compact('applications'));
    }

    /**
     * COMPLETED JOBS
     */
    public function completedJobs()
    {
        $client = Client::where('user_id', Auth::id())->firstOrFail();

        $completedJobs = Job::where('client_id', $client->id)
            ->where('status', 'completed')
            ->with('worker.user')
            ->get();

        return view('client.completed-jobs', compact('completedJobs'));
    }
}
