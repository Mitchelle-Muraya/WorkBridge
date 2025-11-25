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

        // Ensure client record always exists
        $client = Client::firstOrCreate(
            ['user_id' => $userId],
            ['company' => Auth::user()->name . ' Company', 'photo' => null]
        );

        $clientId = $client->id;

        $totalJobs = Job::where('client_id', $clientId)->count();
        $jobsInProgress = Job::where('client_id', $clientId)
            ->where('status', 'in_progress')->count();
        $completedJobs = Job::where('client_id', $clientId)
            ->where('status', 'completed')->count();

        $applications = Application::whereHas('job', function ($q) use ($clientId) {
            $q->where('client_id', $clientId);
        })
        ->with(['user', 'job'])
        ->latest()
        ->take(5)
        ->get();

        $recommended = json_decode($client->recommended_workers ?? '[]', true);

        $newApplications = Application::whereHas('job', function ($q) use ($clientId) {
            $q->where('client_id', $clientId);
        })
        ->where('viewed', false)
        ->count();

        return view('dashboard.client', compact(
            'totalJobs', 'jobsInProgress', 'completedJobs',
            'applications', 'recommended', 'newApplications'
        ));
    }

    /**
     * 🧱 SHOW JOB CREATION FORM
     */
    public function createJob()
    {
        $rates = SkillRate::all();
        return view('client.post-job', compact('rates'));
    }

    /**
     * 💾 STORE NEW JOB
     */
    public function storeJob(Request $request)
    {
        $user = Auth::user();

        $client = Client::firstOrCreate(
            ['user_id' => $user->id],
            ['company' => $user->name . ' Company', 'photo' => null]
        );

        if ($user->mode !== 'client') {
            $user->update(['mode' => 'client']);
        }

        // Skills handling
        $skills = $request->input('skills_required', []);
        if ($request->filled('other_skill')) {
            $skills[] = $request->other_skill;
        }
        $skillsString = implode(', ', $skills);

        // Validate inputs
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
         * 🔥 GET AI WORKER RECOMMENDATIONS
         */
        try {
            $response = Http::post('http://127.0.0.1:10000/recommend_workers', [
                'skills' => $validated['skills_required']
            ]);

            if ($response->successful()) {
                $data = $response->json();

                $recommended = $data['recommended_workers'] ?? [];

                $workers = collect($recommended)->map(function ($w) {
                    return [
                        'user_id' => $w['user_id'] ?? null,
                        'name' => $w['worker_name'] ?? $w['name'] ?? 'Unnamed Worker',
                        'skills' => $w['skills'] ?? 'N/A',
                        'photo' => $w['photo'] ?? 'https://ui-avatars.com/api/?name=' . urlencode($w['worker_name'] ?? 'Worker'),
                        'location' => $w['location'] ?? 'Nairobi, Kenya',
                        'rating' => rand(3, 5),
                    ];
                });

                // Prevent recommending the client themselves
                $workers = $workers->reject(fn($w) => $w['user_id'] == $user->id)->values();

                $client->recommended_workers = json_encode($workers);
                $client->save();
            }
        } catch (\Exception $e) {
            \Log::error('FLASK ERROR: ' . $e->getMessage());
        }

        return redirect()->route('client.dashboard')
            ->with('success', 'Job posted successfully! AI recommendations refreshed.');
    }

    /**
     * 📋 LIST ALL JOBS
     */
    public function myJobs()
    {
        $client = Client::where('user_id', Auth::id())->first();

        if (!$client) {
            return redirect()->route('dashboard')
                ->with('error', 'Client profile not found.');
        }

        $jobs = Job::where('client_id', $client->id)->latest()->get();
        return view('client.my-jobs', compact('jobs'));
    }

    public function editJob($id)
    {
        $client = Client::where('user_id', Auth::id())->first();

        $job = Job::where('id', $id)
            ->where('client_id', $client->id)
            ->firstOrFail();

        return view('client.edit-job', compact('job'));
    }

    public function updateJob(Request $request, $id)
    {
        $client = Client::where('user_id', Auth::id())->first();

        $job = Job::where('id', $id)
            ->where('client_id', $client->id)
            ->firstOrFail();

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

    public function deleteJob($id)
    {
        $client = Client::where('user_id', Auth::id())->first();

        $job = Job::where('id', $id)
            ->where('client_id', $client->id)
            ->firstOrFail();

        $job->delete();

        return redirect()->route('client.my-jobs')
            ->with('success', 'Job deleted successfully.');
    }

    public function viewApplications()
    {
        $client = Client::where('user_id', Auth::id())->first();

        $applications = Application::whereHas('job', fn($q) =>
            $q->where('client_id', $client->id)
        )->with(['user', 'job'])->latest()->get();

        // Mark all as viewed
        Application::whereHas('job', fn($q) =>
            $q->where('client_id', $client->id)
        )->update(['viewed' => true]);

        return view('client.applications', compact('applications'));
    }

    public function completedJobs()
    {
        $client = Client::where('user_id', auth()->id())->first();

        $completedJobs = Job::where('client_id', $client->id)
            ->where('status', 'completed')
            ->with('worker.user')
            ->get();

        return view('client.completed-jobs', compact('completedJobs'));
    }
}
