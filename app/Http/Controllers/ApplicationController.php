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
     * 🟦 WORKER APPLIES FOR A JOB
     */
    public function apply($jobId)
    {
        $userId = Auth::id();

        // Prevent duplicate applications
        if (Application::where('user_id', $userId)->where('job_id', $jobId)->exists()) {
            return back()->with('info', 'You have already applied for this job.');
        }

        // Save application
        $application = Application::create([
            'user_id' => $userId,
            'job_id' => $jobId,
            'applied_at' => now(),
            'status' => 'pending',
        ]);

        // Notify the job owner
        $job = Job::find($jobId);

        if ($job && $job->client_id) {
            Notification::create([
                'user_id' => $job->client_id,  // FIXED: client is just user_id
                'title' => 'New Job Application',
                'message' => Auth::user()->name . ' applied for your job "' . $job->title . '".'
            ]);
        }

        return back()->with('success', 'Application submitted successfully!');
    }

    /**
     * 🟧 CLIENT ACCEPTS OR REJECTS APPLICATION
     */
    public function updateStatus(Request $request, $id)
    {
        $application = Application::findOrFail($id);
        $newStatus = $request->status;

        // Update application
        $application->update(['status' => $newStatus]);

        // =============== ACCEPTED ===============
        if ($newStatus === 'accepted') {

            // Assign worker + update job
            $application->job->update([
                'worker_id' => $application->user_id,
                'status' => 'in_progress',
            ]);

            // Notify worker
            Notification::create([
                'user_id' => $application->user_id,
                'title' => 'Application Accepted',
                'message' => '🎉 Your application for "' . $application->job->title . '" has been accepted.'
            ]);
        }

        // =============== REJECTED ===============
        if ($newStatus === 'rejected') {

            Notification::create([
                'user_id' => $application->user_id,
                'title' => 'Application Rejected',
                'message' => '❌ Your application for "' . $application->job->title . '" has been rejected.'
            ]);
        }

        return back()->with('success', "Application {$newStatus} successfully.");
    }
}
