<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Message;
use App\Models\Notification;
use App\Models\User;

class ChatController extends Controller
{
    /**
     * 📨 Show chat between client & worker for a given job
     */
    public function showChat($jobId, $receiverId)
    {
        $messages = Message::where(function ($q) use ($jobId, $receiverId) {
                $q->where('sender_id', Auth::id())
                  ->where('receiver_id', $receiverId);
            })
            ->orWhere(function ($q) use ($jobId, $receiverId) {
                $q->where('sender_id', $receiverId)
                  ->where('receiver_id', Auth::id());
            })
            ->where('job_id', $jobId)
            ->orderBy('created_at', 'asc')
            ->get();

        return view('chat.index', compact('messages', 'receiverId', 'jobId'));
    }

    /**
     * 💬 Send new message
     */
    public function send(Request $request)
    {
        $request->validate(['message' => 'required|string|max:1000']);

        $msg = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'job_id' => $request->job_id,
            'message' => $request->message,
        ]);

        // Create notification for receiver
        Notification::create([
            'user_id' => $request->receiver_id,
            'title' => 'New Message',
            'message' => Auth::user()->name . ': ' . substr($request->message, 0, 40),
        ]);

        return response()->json(['success' => true, 'message' => $msg]);
    }

    /**
     * 🔁 Fetch latest messages (AJAX)
     */
    public function fetch($jobId, $receiverId)
    {
        $messages = Message::where(function ($q) use ($jobId, $receiverId) {
                $q->where('sender_id', Auth::id())
                  ->where('receiver_id', $receiverId);
            })
            ->orWhere(function ($q) use ($jobId, $receiverId) {
                $q->where('sender_id', $receiverId)
                  ->where('receiver_id', Auth::id());
            })
            ->where('job_id', $jobId)
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($messages);
    }

    /**
     * 📬 Show inbox page (Messages tab)
     */
    public function index()
{
    $userId = Auth::id();

    // Get recent chats
    $recentChats = Message::where('sender_id', $userId)
        ->orWhere('receiver_id', $userId)
        ->latest()
        ->first();

    // If user has previous chat, open it
    if ($recentChats) {
        $receiverId = $recentChats->sender_id == $userId ? $recentChats->receiver_id : $recentChats->sender_id;
        $jobId = $recentChats->job_id;

        $messages = Message::where('job_id', $jobId)
            ->where(function ($q) use ($userId, $receiverId) {
                $q->where('sender_id', $userId)->where('receiver_id', $receiverId);
            })
            ->orWhere(function ($q) use ($userId, $receiverId) {
                $q->where('sender_id', $receiverId)->where('receiver_id', $userId);
            })
            ->orderBy('created_at', 'asc')
            ->get();

        return view('messages.index', compact('messages', 'receiverId', 'jobId'));
    }

    // Otherwise show default blank chat view
    return view('messages.index', ['messages' => []]);
}


    /**
     * 🧠 Return chat list (for sidebar or badges)
     */
    public function chatList()
    {
        $userId = Auth::id();

        $recentChats = Message::select('job_id', 'receiver_id', 'sender_id')
            ->where('sender_id', $userId)
            ->orWhere('receiver_id', $userId)
            ->latest()
            ->get()
            ->unique(fn($m) => $m->job_id . '-' . $m->receiver_id)
            ->map(function ($m) use ($userId) {
                $receiverId = $m->sender_id === $userId ? $m->receiver_id : $m->sender_id;
                $user = User::find($receiverId);

                $unread = Message::where('receiver_id', $userId)
                    ->where('sender_id', $receiverId)
                    ->where('is_read', false)
                    ->count();

                return [
                    'job_id' => $m->job_id,
                    'receiver_id' => $receiverId,
                    'name' => $user?->name ?? 'Unknown User',
                    'avatar' => "https://ui-avatars.com/api/?name=" . urlencode($user?->name ?? 'U') . "&background=00b3ff&color=fff",
                    'unread' => $unread,
                ];
            });

        return response()->json($recentChats->values());
    }

public function markAsRead($jobId, $receiverId)
{
    Message::where('job_id', $jobId)
        ->where('receiver_id', Auth::id())
        ->where('sender_id', $receiverId)
        ->update(['is_read' => true]);

    return response()->json(['success' => true]);
}

}
