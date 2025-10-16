<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Worker;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    /**
     * Show onboarding form
     */
    public function showOnboarding()
    {
        return view('onboarding.setup');
    }

    /**
     * Handle profile submission and mark completion
     */
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

        // Store or update worker profile
        Worker::updateOrCreate(
            ['user_id' => $user->id],
            [
                'skills' => $request->skills,
                'experience' => $request->experience,
            ]
        );

        // ✅ Mark profile as complete
        $user->is_profile_complete = true;
        $user->save();

        // ✅ Redirect to landing page
        return redirect()
            ->route('landing')
            ->with('success', '🎉 Profile setup complete! You can now apply for jobs.');
    }
}
