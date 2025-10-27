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
     */public function saveProfile(Request $request)
{
    $userId = Auth::id();

    $request->validate([
        'skills' => 'required|string|max:255',
        'experience' => 'nullable|string|max:255',
        'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        'resume' => 'nullable|mimes:pdf,doc,docx|max:4096',
    ]);

    // Find or create worker
    $worker = \App\Models\Worker::firstOrNew(['user_id' => $userId]);
    $worker->skills = $request->skills;
    $worker->experience = $request->experience;

    // ✅ Handle photo upload safely
    if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
        $photoPath = $request->file('photo')->store('profiles', 'public');
        $worker->photo = $photoPath;
    }

    // ✅ Handle resume upload safely
    if ($request->hasFile('resume') && $request->file('resume')->isValid()) {
        $resumePath = $request->file('resume')->store('resumes', 'public');
        $worker->resume = $resumePath;
    }

    $worker->save();

    return redirect()
        ->route('worker.profile')
        ->with('success', 'Profile saved successfully!');
}
}
