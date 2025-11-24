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

        // ⛔ Prevent duplicate applications
        if (Application::where('user_id', $userId)->where('job_id', $jobId)->exists()) {
            return back()->with('info', 'You have already applied for this job.');
        }

        // Create the application
        $application = Application::create([
            'user_id' => $userId,
            'job_id' => $jobId,
            'applied_at' => now(),
            'status' => 'pending',
        ]);

        // Notify the job owner (client)
        $job = Job::find($jobId);
        if ($job && $job->client_id) {
            Notification::create([
                'user_id' => $job->client->user_id,  // client receives notification
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

        // Update application status
        $application->update(['status' => $newStatus]);

        // ----------------------------------------------
        // 🟢 If ACCEPTED
        // ----------------------------------------------
        if ($newStatus === 'accepted') {

            // Link job to chosen worker + change job status
            $application->job->update([
                'worker_id' => $application->user_id,
                'status' => 'in_progress',
            ]);

            // 🔴 Add red-dot notification to worker
            $application->user->update([
                'application_notification' => 1
            ]);

            // Push notification to worker
            Notification::create([
                'user_id' => $application->user_id,
                'title' => 'Application Accepted',
                'message' => '🎉 Your application for "' . $application->job->title . '" has been accepted.'
            ]);
        }

        // ----------------------------------------------
        // 🔴 If REJECTED
        // ----------------------------------------------
        if ($newStatus === 'rejected') {

            // Notify worker
            $application->user->update(['application_notification' => 1]);

            Notification::create([
                'user_id' => $application->user_id,
                'title' => 'Application Rejected',
                'message' => '❌ Your application for "' . $application->job->title . '" has been rejected.'
            ]);
        }

        return back()->with('success', "Application {$newStatus} successfully.");
    }

}

