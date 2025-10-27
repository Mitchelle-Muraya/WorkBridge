<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Job;
use App\Models\Application;
use App\Models\Worker;

class AdminController extends Controller
{
    /**
     * Display the main admin dashboard.
     */
    public function index()
    {
        // Summary stats for quick overview
        $totalUsers = User::count();
        $totalJobs = Job::count();
        $totalApplications = Application::count();

        return view('admin.dashboard', compact('totalUsers', 'totalJobs', 'totalApplications'));
    }

    /**
     * Display the admin reports page with charts and filters.
     */
   public function reports(Request $request)
{
    // 1️⃣ Handle date filters (optional)
    $start = $request->get('start');
    $end = $request->get('end');

    if (!$start) $start = now()->subDays(30)->format('Y-m-d');
    if (!$end) $end = now()->format('Y-m-d');

  // 2️⃣ Job statistics (for pie chart)
$jobStatusData = [
    'pending' => \App\Models\Job::where('status', 'pending')->count(),
    'in_progress' => \App\Models\Job::where('status', 'in_progress')->count(),
    'completed' => \App\Models\Job::where('status', 'completed')->count(),
];

// 3️⃣ Application statistics (for bar chart)
$applicationData = [
    'pending' => \App\Models\Application::where('status', 'pending')->count(),
    'approved' => \App\Models\Application::where('status', 'approved')->count(),
    'rejected' => \App\Models\Application::where('status', 'rejected')->count(),
];


    // 4️⃣ Summary counts
    $totalJobs = \App\Models\Job::count();
    $totalApplications = \App\Models\Application::count();
    $totalUsers = \App\Models\User::count();
    $activeWorkers = \App\Models\Worker::whereNotNull('skills')->count();
// 6️⃣ Top 5 highest-rated workers
$topWorkers = \App\Models\Review::select('worker_id')
    ->selectRaw('AVG(rating) as avg_rating, COUNT(*) as total_reviews')
    ->groupBy('worker_id')
    ->orderByDesc('avg_rating')
    ->limit(5)
    ->with('worker.user') // assuming Worker belongsTo User
    ->get();

    // 5️⃣ Send all variables to view
    return view('admin.reports.index', compact(
        'start',
        'end',
        'jobStatusData',
        'applicationData',
        'totalJobs',
        'totalApplications',
        'totalUsers',
        'activeWorkers',
        'topWorkers'
    ));
}
 // 🧍‍♀️ Manage Users
    public function manageUsers()
    {
        $users = User::all(); // fetch all registered users
        return view('admin.users', compact('users'));
    }

    // 💼 Manage Jobs
    public function manageJobs()
    {
        $jobs = Job::with('client')->get(); // load jobs with their client relationship
        return view('admin.jobs', compact('jobs'));
    }
// ❌ Delete Job
    public function deleteJob($id)
    {
        $job = Job::findOrFail($id);
        $job->delete();

        return back()->with('success', 'Job deleted successfully!');
    }

    // ✅ Activate User
    public function activateUser($id)
    {
        $user = User::findOrFail($id);
        $user->status = 'active';
        $user->save();

        return back()->with('success', 'User activated successfully!');
    }

    // 🚫 Deactivate User
    public function deactivateUser($id)
    {
        $user = User::findOrFail($id);
        $user->status = 'inactive';
        $user->save();

        return back()->with('success', 'User deactivated successfully!');
    }

    // ❌ Delete User
    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return back()->with('success', 'User deleted successfully!');
    }



}
