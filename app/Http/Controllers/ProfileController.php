<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Worker;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function showOnboarding()
    {
        // If user already finished onboarding, skip it
        if (Auth::user()->profile_status === 'complete') {
            return redirect()->route('worker.dashboard');
        }

        return view('onboarding.setup');
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        // Validate input
        $request->validate([
            'phone' => 'required|string|max:15',
            'location' => 'required|string|max:255',
            'skills' => 'nullable|string|max:255',
            'experience' => 'nullable|string|max:255',
            'resume' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'photo' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
        ]);

        // ✅ Create or update worker profile
        Worker::updateOrCreate(
            ['user_id' => $user->id],
            [
                'skills' => $request->skills,
                'experience' => $request->experience,
                'photo' => $request->photo ? $request->file('photo')->store('photos', 'public') : null,
                'resume' => $request->resume ? $request->file('resume')->store('resumes', 'public') : null,
            ]
        );

        // ✅ Mark user as complete
        $user->update(['profile_status' => 'complete']);

        // Redirect to landing with message
        return redirect()->route('landing')->with('success', '🎉 Profile setup complete! You can now browse and apply for jobs.');
    }
}
