<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Job;
use App\Models\Client;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $client = Client::where('user_id', $user->id)->first();

        return view('client.dashboard', compact('client'));
    }

    public function postedJobs()
    {
        $user = Auth::user();
        $client = Client::where('user_id', $user->id)->first();

        $jobs = $client ? Job::where('client_id', $client->id)->get() : [];

        return view('client.jobs', compact('jobs'));
    }

    public function postJob(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'skills' => 'required'
        ]);

        $user = Auth::user();
        $client = Client::where('user_id', $user->id)->first();

        if ($client) {
            $job = new Job();
            $job->client_id = $client->id;
            $job->title = $request->title;
            $job->description = $request->description;
            $job->extracted_skills = $request->skills;
            $job->save();
        }

        return back()->with('success', 'Job posted successfully!');
    }
}
