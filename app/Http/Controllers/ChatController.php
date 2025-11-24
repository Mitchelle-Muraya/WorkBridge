<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Message;
use App\Models\Notification;
use App\Models\User;
use App\Events\MessageSent; // <-- IMPORTANT

class ChatController extends Controller
{
    public function index()
    {
        return view('messages.index', [
            'messages' => [],
            'receiverId' => null,
            'jobId' => null,
            'autoOpen' => false
        ]);
    }

    public function startChat($jobId, $receiverId)
    {
        $receiver = User::find($receiverId);

        return view('messages.index', [
            'messages' => [],
            'receiverId' => $receiverId,
            'jobId' => $jobId,
            'receiverName' => $receiver->name,
            'receiverAvatar' => "https://ui-avatars.com/api/?name=" . urlencode($receiver->name) . "&background=00b3ff&color=fff",
            'autoOpen' => true,
        ]);
    }

    public function fetch($jobId, $receiverId)
    {
        $messages = Message::where('job_id', $jobId)
            ->where(function ($q) use ($receiverId) {
                $q->where(function ($x) use ($receiverId) {
                    $x->where('sender_id', Auth::id())
                      ->where('receiver_id', $receiverId);
                })
                ->orWhere(function ($x) use ($receiverId) {
                    $x->where('sender_id', $receiverId)
                      ->where('receiver_id', Auth::id());
                });
            })
            ->orderBy("created_at", "asc")
            ->get();

        return response()->json($messages);
    }

    public function send(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000'
        ]);

        // ✔ Save message
        $msg = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'job_id' => $request->job_id,
            'message' => $request->message,
            'is_read' => 0
        ]);

        // ✔ Broadcast in REAL TIME
        event(new MessageSent($msg));

        // ✔ Create notification
        Notification::create([
            'user_id' => $request->receiver_id,
            'title' => 'New Message',
            'message' => Auth::user()->name . ': ' . substr($request->message, 0, 40),
        ]);

        return response()->json(['success' => true]);
    }

     public function chatList()
    {
        $userId = Auth::id();

        $messages = Message::where(function ($q) use ($userId) {
                $q->where('sender_id', $userId)
                  ->orWhere('receiver_id', $userId);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $convos = [];

        foreach ($messages as $m) {
            $otherId = $m->sender_id == $userId ? $m->receiver_id : $m->sender_id;

            if ($otherId == $userId) continue; // PREVENT SELF CHAT

            $key = "{$m->job_id}-{$otherId}";

            if (!isset($convos[$key])) {

                $other = User::find($otherId);

                $unread = Message::where([
                        ['receiver_id', $userId],
                        ['sender_id', $otherId],
                        ['is_read', 0]
                    ])->count();

                $convos[$key] = [
                    'job_id'      => $m->job_id,
                    'receiver_id' => $otherId,   // FIXED KEY NAME
                    'name'        => $other->name,
                    'avatar'      => "https://ui-avatars.com/api/?name=" . urlencode($other->name) . "&background=00b3ff&color=fff",
                    'unread'      => $unread
                ];
            }
        }

        return response()->json(array_values($convos));
    }


   public function markAsRead($jobId, $receiverId)
{
    Message::where('job_id', $jobId)
        ->where('receiver_id', Auth::id())
        ->where('sender_id', $receiverId)
        ->update(['is_read' => 1]);

    event(new ReadReceiptSent($jobId, Auth::id(), $receiverId));

    return response()->json(['success' => true]);
}


}
