<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Models\Job;   // adjust to your Job model namespace
use App\Models\User;  // or Worker model

class MatcherController extends Controller
{
    protected $client;
    protected $matcherUrl;

    public function __construct()
    {
        $this->client = new Client(['timeout' => 10]);
        $this->matcherUrl = env('MATCHER_URL', 'http://127.0.0.1:5000');
    }

    // Match workers for a job in DB
    public function matchJob($jobId)
    {
        $job = Job::findOrFail($jobId);

        // Build skills_text: if you store extracted_skills as JSON in DB, use it.
        $skills = $job->extracted_skills ?? null;
        if (is_array($skills)) {
            $skills_text = implode(' ', $skills);
        } else {
            $skills_text = trim($job->title . ' ' . $job->description);
        }

        $resp = $this->client->post($this->matcherUrl . '/match_job', [
            'json' => [
                'skills_text' => $skills_text,
                'top_n' => 10
            ]
        ]);

        $data = json_decode((string)$resp->getBody(), true);
        // data['results'] contains worker_index and similarity and worker text.

        return response()->json($data);
    }

    // Match jobs for a worker profile
    public function matchWorker($workerIndexOrIdOrProfile)
    {
        // Example expects POST body with skills_text, or pass an index.
        $payload = request()->all();
        $resp = $this->client->post($this->matcherUrl . '/match_worker', [
            'json' => $payload
        ]);
        $data = json_decode((string)$resp->getBody(), true);
        return response()->json($data);
    }
}
