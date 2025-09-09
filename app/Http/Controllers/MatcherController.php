<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class MatcherController extends Controller
{
    protected $client;
    protected $matcherUrl;

    public function __construct()
    {
        $this->client = new Client(['timeout' => 10]);
        $this->matcherUrl = env('MATCHER_URL', 'http://127.0.0.1:5000');
    }

    // For testing: match workers for a fake job
    public function matchJob($jobId)
    {
        // Hardcoded test input (later we’ll fetch from DB)
        $skills_text = "plumbing electrical repairs";

        try {
            $resp = $this->client->post($this->matcherUrl . '/match_job', [
                'json' => [
                    'skills_text' => $skills_text,
                    'top_n' => 5
                ]
            ]);

            $data = json_decode((string)$resp->getBody(), true);
            return response()->json($data);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
