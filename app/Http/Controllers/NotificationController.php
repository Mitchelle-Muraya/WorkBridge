<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\Application;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Fetch all unread notifications for the logged-in user.
     */
    public function fetch()
{
    $userId = Auth::id();

    // Only get notifications about job actions (applied / accepted / rejected)
    $notifications = Notification::where('user_id', $userId)
        ->where(function($q) {
            $q->where('title', 'LIKE', '%Job%')
              ->orWhere('title', 'LIKE', '%Application%')
              ->orWhere('title', 'LIKE', '%Approved%')
              ->orWhere('title', 'LIKE', '%Rejected%');
        })
        ->where('is_read', false)
        ->latest()
        ->take(10)
        ->get();

    return response()->json($notifications);
}

    /**
     * Mark all notifications as read.
     */
    public function markAllRead()
    {
        Notification::where('user_id', Auth::id())
            ->update(['read' => true]);

        return response()->json(['success' => true]);
    }

    /**
     * Send a notification when a job is accepted by the client.
     */
    public function jobAccepted($applicationId)
    {
        $application = Application::with(['user', 'job'])->findOrFail($applicationId);

        // Receiver is the worker who applied
        $receiver_id = $application->user_id;
        $client = Auth::user();

        Notification::create([
            'user_id' => $receiver_id,
            'title' => 'Job Accepted',
            'message' => "{$client->name} has accepted your job request for '{$application->job->title}'.",
        ]);

        return response()->json(['success' => true, 'message' => 'Notification sent (job accepted).']);
    }

    /**
     * Send a notification when a job is rejected by the client.
     */
    public function jobRejected($applicationId)
    {
        $application = Application::with(['user', 'job'])->findOrFail($applicationId);

        // Receiver is the worker who applied
        $receiver_id = $application->user_id;
        $client = Auth::user();

        Notification::create([
            'user_id' => $receiver_id,
            'title' => 'Job Rejected',
            'message' => "{$client->name} has rejected your job request for '{$application->job->title}'.",
        ]);

        return response()->json(['success' => true, 'message' => 'Notification sent (job rejected).']);
    }
}
