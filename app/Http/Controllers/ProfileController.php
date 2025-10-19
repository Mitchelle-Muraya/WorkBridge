<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Worker;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    /**
     * Show Worker Profile Form
     */
    public function showForm()
    {
        $worker = Worker::where('user_id', Auth::id())->first();
        return view('worker.profile', compact('worker'));
    }

    /**
     * Save or Update Worker Profile
     */
    public function saveProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'skills' => 'required|string|max:255',
            'experience' => 'required|string|max:255',
            'photo' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
            'resume' => 'nullable|file|mimes:pdf,doc,docx|max:4096',
        ]);

        $photoPath = $request->hasFile('photo')
            ? $request->file('photo')->store('photos', 'public')
            : null;

        $resumePath = $request->hasFile('resume')
            ? $request->file('resume')->store('resumes', 'public')
            : null;

        Worker::updateOrCreate(
            ['user_id' => $user->id],
            [
                'skills' => $validated['skills'],
                'experience' => $validated['experience'],
                'photo' => $photoPath,
                'resume' => $resumePath,
            ]

        );
        $user->update(['profile_status' => 'complete']);



        return redirect()->route('worker.dashboard')
            ->with('success', '🎉 Profile completed successfully! You can now apply for jobs.');
    }
}
