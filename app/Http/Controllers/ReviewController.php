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


}
