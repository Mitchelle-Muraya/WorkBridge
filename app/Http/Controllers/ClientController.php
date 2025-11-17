<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Models\Job;
use App\Models\Application;
use App\Models\Client;

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
        })->with(['user', 'job'])->latest()->take(5)->get();

        // 🧠 Load any previously saved recommendations
        $recommended = json_decode($client->recommended_workers ?? '[]', true);

        return view('dashboard.client', compact(
            'totalJobs', 'jobsInProgress', 'completedJobs', 'applications', 'recommended'
        ));
    }

    /**
     * 🧱 SHOW JOB CREATION FORM
     */
    public function createJob()
    {
        return view('client.post-job');
    }

    /**
     * 💾 STORE A NEW JOB & GET AI RECOMMENDATIONS
     */
    public function storeJob(Request $request)
    {
        $user = Auth::user();

        // ✅ Ensure client record exists
        $client = Client::firstOrCreate(
            ['user_id' => $user->id],
            ['company' => $user->name . ' Company', 'photo' => null]
        );

        // Switch to client mode if needed
        if ($user->mode !== 'client') {
            $user->update(['mode' => 'client']);
        }

        // ✅ Combine skills and "other_skill"
        $skills = $request->input('skills_required', []);
        if ($request->filled('other_skill')) {
            $skills[] = $request->input('other_skill');
        }

        $skillsString = implode(', ', $skills);

        // ✅ Validate job inputs
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'budget' => 'required|numeric|min:0',
            'deadline' => 'required|date|after:today',
            'location' => 'required|string|max:255',
        ]);

        // ✅ Add additional job fields
        $validated['skills_required'] = $skillsString;
        $validated['client_id'] = $client->id;
        $validated['user_id'] = $user->id;
        $validated['status'] = 'pending';

        // ✅ Save the job in DB
        $job = Job::create($validated);


        try {
            $response = Http::post('http://127.0.0.1:10000/recommend_workers', [
                'skills' => $validated['skills_required']
            ]);

            if ($response->successful()) {
                $data = $response->json();
\Log::info('FLASK RESPONSE:', $data);

                // Expecting structure: { "recommended_workers": [ ... ] }
                $recommended = $data['recommended_workers'] ?? [];

                // Map and clean up worker data
                $workers = collect($recommended)->map(function ($w) {
                    return [
                        'name' => $w['worker_name'] ?? $w['name'] ?? 'Unnamed Worker',
                        'skills' => $w['skills'] ?? 'N/A',
                        'photo' => $w['photo'] ?? 'https://ui-avatars.com/api/?name=' . urlencode($w['worker_name'] ?? 'Worker'),
                        'location' => $w['location'] ?? 'Nairobi, Kenya',
                        'rating' => rand(3, 5),
                    ];
                });

                // Save permanently in client record
                $client->recommended_workers = json_encode($workers);
                $client->save();
            } else {
                \Log::warning('Flask API failed: ' . $response->body());
            }
        } catch (\Exception $e) {
            \Log::error('Worker recommendation failed: ' . $e->getMessage());
        }

        // ✅ Redirect to dashboard
        return redirect()->route('client.dashboard')
            ->with('success', 'Job posted successfully! AI-recommended workers updated.');
    }

    /**
     * 📋 LIST ALL JOBS POSTED BY THIS CLIENT
     */
    public function myJobs()
    {
        $userId = Auth::id();
        $client = Client::where('user_id', $userId)->first();

        if (!$client) {
            return redirect()->route('dashboard')
                ->with('error', 'Client profile not found.');
        }

        $jobs = Job::where('client_id', $client->id)->latest()->get();
        return view('client.my-jobs', compact('jobs'));
    }

    public function messages()
    {
        return view('messages.index');
    }

    public function reviews()
    {
        return view('client.reviews');
    }

    /**
     * 📬 VIEW JOB APPLICATIONS
     */
    public function viewApplications()
    {
        $userId = Auth::id();
        $client = Client::where('user_id', $userId)->first();

        if (!$client) {
            return redirect()->route('dashboard')
                ->with('error', 'Client profile not found.');
        }

        $applications = Application::whereHas('job', fn($q) =>
            $q->where('client_id', $client->id)
        )->with(['user', 'job'])->latest()->get();

        return view('client.applications', compact('applications'));
    }

    public function applications()
    {
        return $this->viewApplications();
    }
}
