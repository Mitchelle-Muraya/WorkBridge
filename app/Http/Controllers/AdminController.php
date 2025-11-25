<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Job;
use App\Models\Application;
use App\Models\Worker;
use App\Models\Review;

class AdminController extends Controller
{
    /**
     * MAIN ADMIN DASHBOARD
     */
    public function index()
    {
        return view('admin.dashboard', [
            'totalUsers'        => User::count(),
            'totalJobs'         => Job::count(),
            'totalApplications' => Application::count(),
        ]);
    }


    /**
     * REPORTS DASHBOARD (FILTERABLE)
     */
    public function reports(Request $request)
    {
        // -------------------------------------------------------
        // 1️⃣ GET FILTER INPUTS
        // -------------------------------------------------------
        $start     = $request->start_date ?? now()->subDays(30)->format('Y-m-d');
        $end       = $request->end_date   ?? now()->format('Y-m-d');
        $status    = $request->status     ?? '';
        $location  = $request->location   ?? '';
        $category  = $request->category   ?? ''; // skills_required column


        // -------------------------------------------------------
        // 2️⃣ BUILD DYNAMIC QUERY
        // -------------------------------------------------------
        $query = Job::query()
            ->whereBetween('created_at', [$start, $end]);

        if (!empty($status)) {
            $query->where('status', $status);
        }

        if (!empty($location)) {
            $query->where('location', 'LIKE', "%{$location}%");
        }

        if (!empty($category)) {
            $query->where('skills_required', 'LIKE', "%{$category}%");
        }

        $filteredJobs = $query->get();


        // -------------------------------------------------------
        // 3️⃣ SUMMARY CARDS (FILTERED)
        // -------------------------------------------------------
        $totalJobs         = $filteredJobs->count();
        $totalApplications = Application::count();
        $totalUsers        = User::count();
        $activeWorkers     = Worker::whereNotNull('skills')->count();


        // -------------------------------------------------------
        // 4️⃣ JOB STATUS CHART DATA (FILTERED)
        // -------------------------------------------------------
        $jobStatusData = [
            'pending'     => $query->clone()->where('status', 'pending')->count(),
            'in_progress' => $query->clone()->where('status', 'in_progress')->count(),
            'completed'   => $query->clone()->where('status', 'completed')->count(),
        ];


        // -------------------------------------------------------
        // 5️⃣ APPLICATION CHART (GLOBAL OR FILTERED)
        // -------------------------------------------------------
        $applicationData = [
            'pending'  => Application::where('status', 'pending')->count(),
            'approved' => Application::where('status', 'approved')->count(),
            'rejected' => Application::where('status', 'rejected')->count(),
        ];


        // -------------------------------------------------------
        // 6️⃣ TOP WORKERS
        // -------------------------------------------------------
        $topWorkers = Review::select('worker_id')
            ->selectRaw('AVG(rating) as avg_rating, COUNT(*) as total_reviews')
            ->groupBy('worker_id')
            ->orderByDesc('avg_rating')
            ->limit(5)
            ->with('worker.user')
            ->get();


        // -------------------------------------------------------
        // 7️⃣ RETURN TO VIEW
        // -------------------------------------------------------
        return view('admin.reports.index', [
            'start'             => $start,
            'end'               => $end,
            'status'            => $status,
            'location'          => $location,
            'category'          => $category,
            'filteredJobs'      => $filteredJobs,
            'jobStatusData'     => $jobStatusData,
            'applicationData'   => $applicationData,
            'totalJobs'         => $totalJobs,
            'totalApplications' => $totalApplications,
            'totalUsers'        => $totalUsers,
            'activeWorkers'     => $activeWorkers,
            'topWorkers'        => $topWorkers,
        ]);
    }


    /**
     * MANAGE USERS PAGE
     */
    public function manageUsers()
    {
        return view('admin.users', [
            'users' => User::all(),
            'userStats' => [
                'worker' => User::where('role', 'worker')->count(),
                'client' => User::where('role', 'client')->count(),
            ]
        ]);
    }


    /**
     * MANAGE JOBS PAGE
     */
    public function manageJobs()
    {
        $jobs = Job::with('client')->get();
        return view('admin.jobs', compact('jobs'));
    }


    /**
     * DELETE JOB
     */
    public function deleteJob($id)
    {
        Job::findOrFail($id)->delete();
        return back()->with('success', 'Job deleted successfully.');
    }


    /**
     * ACTIVATE USER
     */
    public function activateUser($id)
{
    $user = User::findOrFail($id);
    $user->status = 'active';
    $user->save();

    return redirect()->back()->with('success', 'User activated successfully');
}

public function deactivateUser($id)
{
    $user = User::findOrFail($id);
    $user->status = 'inactive';
    $user->save();

    return redirect()->back()->with('success', 'User deactivated successfully');
}

    /**
     * DELETE USER
     */
    public function deleteUser($id)
    {
        User::findOrFail($id)->delete();
        return back()->with('success', 'User deleted successfully.');
    }

// APPROVE JOB
public function approveJob($id)
{
    $job = Job::findOrFail($id);

    if ($job->status !== 'pending') {
        return back()->with('error', 'Job cannot be approved.');
    }

    $job->status = 'approved';
    $job->save();

    return back()->with('success', 'Job approved successfully.');
}

// REJECT JOB
public function rejectJob($id)
{
    $job = Job::findOrFail($id);

    if ($job->status !== 'pending') {
        return back()->with('error', 'Job cannot be rejected.');
    }

    $job->status = 'rejected';
    $job->save();

    return back()->with('success', 'Job rejected.');
}

// MARK COMPLETED BY ADMIN
public function completeJob($id)
{
    $job = Job::findOrFail($id);

    if ($job->status !== 'in_progress') {
        return back()->with('error', 'Only in-progress jobs can be completed.');
    }

    $job->status = 'completed';
    $job->save();

    return back()->with('success', 'Job marked as completed.');
}

// VIEW FULL JOB DETAILS
public function viewJob($id)
{
    $job = Job::with(['client', 'applications.user'])->findOrFail($id);
    return view('admin.view-job', compact('job'));
}
}
