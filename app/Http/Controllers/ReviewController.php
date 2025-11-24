<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\Job;
use App\Models\Worker;

class ReviewController extends Controller
{
    /**
     * Show all reviews for the logged-in worker
     */
    public function index()
    {
        // Fetch the worker record for the current logged-in user
        $worker = Worker::where('user_id', auth()->id())->first();

        // If the user doesn’t have a worker profile yet
        if (!$worker) {
            return back()->with('info', 'You do not have a worker profile yet.');
        }

        // Fetch reviews for this worker
        $reviews = Review::with(['client', 'job'])
            ->where('worker_id', $worker->id)
            ->latest()
            ->get();

        return view('worker.reviews', compact('reviews', 'worker'));
    }

    /**
     * Store a new review (from client side)
     */
    public function store(Request $request)
{
    $request->validate([
        'job_id' => 'required|exists:jobs,id',
        'worker_id' => 'required|exists:workers,id',
        'rating' => 'required|integer|min:1|max:5',
        'comment' => 'nullable|string|max:500',
    ]);

    $existingReview = Review::where('job_id', $request->job_id)
        ->where('client_id', auth()->id())
        ->first();

    if ($existingReview) {
        return response()->json(['success' => false, 'message' => 'You already reviewed this job.']);
    }

    Review::create([
        'job_id' => $request->job_id,
        'client_id' => auth()->id(),
        'worker_id' => $request->worker_id,
        'rating' => $request->rating,
        'comment' => $request->comment,
    ]);

    return response()->json(['success' => true]);
}
public function workerReviewForm(Job $job)
{
    // Load the client through the job relationship
    $client = $job->client->user;

    return view('worker.review-client', compact('job', 'client'));
}

public function storeWorkerReview(Request $request)
{
    $request->validate([
        'job_id' => 'required|exists:jobs,id',
        'client_id' => 'required|exists:users,id',
        'rating' => 'required|integer|min:1|max:5',
        'review' => 'required|string|max:500',
    ]);

    // Prevent duplicate review
    $exists = Review::where('job_id', $request->job_id)
        ->where('worker_id', auth()->id())
        ->first();

    if ($exists) {
        return back()->with('error', 'You already reviewed this client.');
    }

    Review::create([
        'job_id' => $request->job_id,
        'client_id' => $request->client_id,
        'worker_id' => auth()->id(),
        'rating' => $request->rating,
        'comment' => $request->review,
    ]);

    return redirect()->route('worker.dashboard')
        ->with('success', 'Review submitted successfully!');
}

public function clientReviewForm($jobId)
{
    $job = Job::with('worker.user')->findOrFail($jobId);

    // Worker model from job
    $worker = $job->worker;

    return view('client.reviews', compact('job', 'worker'));
}

public function storeClientReview(Request $request)
{
    $request->validate([
        'job_id'    => 'required|exists:jobs,id',
        'worker_id' => 'required|exists:workers,id',
        'rating'    => 'required|integer|min:1|max:5',
        'review'    => 'nullable|string|max:500',
    ]);

    Review::create([
        'job_id'    => $request->job_id,
        'client_id' => auth()->id(),     // ⭐ Correct client
        'worker_id' => $request->worker_id,
        'rating'    => $request->rating,
        'comment'   => $request->review, // ⭐ Correct column name
    ]);

    return redirect()
        ->route('client.completedJobs')
        ->with('success', 'Review submitted successfully!');
}

}
