<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class JobController extends Controller
{
     public function store(Request $request)
    {
        // 1️⃣ Validate input
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'skills' => 'nullable|string',
        ]);

        // 2️⃣ Call Flask API for prediction
        $response = Http::post('http://127.0.0.1:5000/predict_job', [
            'description' => $validated['description'],
        ]);

        $prediction = $response->json();

        // 3️⃣ Save job with predicted category
        $job = Job::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'skills' => $validated['skills'],
            'predicted_category' => $prediction['predicted_category'], // 👈 save category
            'confidence' => json_encode($prediction['confidence']), // optional: save full confidence levels
        ]);

        return response()->json([
            'message' => 'Job created successfully',
            'job' => $job
        ]);
    }
}
