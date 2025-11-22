<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Job;
use App\Models\Application;
use Illuminate\Support\Facades\Auth;

class JobController extends Controller
{
    // APPLY FOR A JOB
    public function apply($id)
    {
        $workerId = Auth::id();

        $exists = Application::where('user_id', $workerId)->where('job_id', $id)->exists();
        if ($exists) {
            return back()->with('info', 'You already applied for this job.');
        }

        Application::create([
            'user_id' => $workerId,
            'job_id' => $id,
            'applied_at' => now(),
        ]);

        return back()->with('success', 'Application submitted successfully!');
    }

    // MARK JOB AS COMPLETE
   public function complete($id)
{
    $job = Job::findOrFail($id);
    $job->update(['status' => 'completed']);

    return response()->json([
        'success' => true,
        'message' => '✅ Job marked as completed successfully!',
        'job_id' => $job->id,
        'worker_id' => $job->worker_id
    ]);
}

}
