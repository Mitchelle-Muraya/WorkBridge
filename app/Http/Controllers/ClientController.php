<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Job;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{
    public function dashboard() {
        return view('client.dashboard', ['client' => Auth::guard('client')->user()]);
    }

    public function postedJobs() {
        $client = Auth::guard('client')->user();
        $jobs = Job::where('client_id', $client->id)->get();
        return view('client.jobs', ['jobs' => $jobs]);
    }

    public function postJob(Request $request) {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'skills' => 'required'
        ]);

        $client = Auth::guard('client')->user();
        $job = new Job();
        $job->client_id = $client->id;
        $job->title = $request->title;
        $job->description = $request->description;
        $job->extracted_skills = $request->skills; // comma-separated or JSON
        $job->save();

        return back()->with('success', 'Job posted successfully!');
    }
}
