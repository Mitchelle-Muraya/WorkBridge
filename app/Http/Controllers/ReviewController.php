<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\Job;

class ReviewController extends Controller
{
    // Store a new review
    public function store(Request $request, $jobId)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
        ]);

        $job = Job::findOrFail($jobId);

        Review::create([
            'job_id' => $job->id,
            'client_id' => auth()->id(),
            'worker_id' => $job->worker_id, // make sure your Job model has this column
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return redirect()->back()->with('success', '✅ Review submitted successfully!');
    }
}
