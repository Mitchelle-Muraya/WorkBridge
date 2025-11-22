<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Application;
use App\Models\Job;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class ApplicationController extends Controller
{
    /**
     * Apply for a job
     */
    public function apply($id)
    {
        $userId = Auth::id();

        // ✅ Prevent duplicate applications
        $exists = Application::where('user_id', $userId)
            ->where('job_id', $id)
            ->exists();

        if ($exists) {
            return back()->with('info', 'You have already applied for this job.');
        }

        // ✅ Create new application
        $application = Application::create([
            'user_id' => $userId,
            'job_id' => $id,
            'applied_at' => now(),
            'status' => 'pending',
        ]);

        // ✅ Notify the job owner (client)
        $job = Job::find($id);
        if ($job && $job->client_id) {
            Notification::create([
                'user_id' => $job->client_id,
                'title' => 'New Job Application',
                'message' => Auth::user()->name . ' has applied for your job "' . $job->title . '".',
            ]);
        }

        return back()->with('success', '✅ Application submitted successfully!');
    }

    /**
     * Update application status (accept / reject)
     */
    public function updateStatus(Request $request, $id)
{
    $application = Application::findOrFail($id);
    $application->status = $request->status;
    $application->save();

    if ($request->status === 'accepted') {
        $application->job->update([
            'worker_id' => $application->user_id, // ✅ link job to the accepted worker
            'status' => 'in_progress',
        ]);
    }

    Notification::create([
        'user_id' => $application->user_id,
        'title' => 'Job Application ' . ucfirst($request->status),
        'message' => 'Your application for "' . $application->job->title . '" has been ' . $request->status . '.',
    ]);

    return back()->with('success', 'Application ' . $request->status . ' successfully.');
}

}
