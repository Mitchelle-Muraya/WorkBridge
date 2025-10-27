<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Job;
use App\Models\Application;
use App\Models\User;
use Dompdf\Dompdf;
use Dompdf\Options;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReportExport;

class ReportController extends Controller
{
   public function index(Request $request)
{
    // Get optional date filters
    $start = $request->input('start', now()->subMonth()->toDateString());
    $end = $request->input('end', now()->toDateString());

    // Dashboard stats
    $totalJobs = \App\Models\Job::count();
    $totalApplications = \App\Models\Application::count();
    $totalUsers = \App\Models\User::count();
    $activeWorkers = \App\Models\Worker::count();

    // Job status breakdown
    $jobStatusData = [
        'pending' => \App\Models\Job::where('status', 'pending')->count(),
        'in_progress' => \App\Models\Job::where('status', 'in_progress')->count(),
        'completed' => \App\Models\Job::where('status', 'completed')->count(),
    ];

    // Application status breakdown
    $applicationData = [
        'pending' => \App\Models\Application::where('status', 'pending')->count(),
        'accepted' => \App\Models\Application::where('status', 'accepted')->count(),
        'rejected' => \App\Models\Application::where('status', 'rejected')->count(),
    ];

    // 👇 Add this section
    $topWorkers = \App\Models\Worker::withAvg('reviews', 'rating')
        ->orderByDesc('reviews_avg_rating')
        ->take(5)
        ->get();

    // Send everything to the view
    return view('admin.reports.index', compact(
        'start',
        'end',
        'totalJobs',
        'totalApplications',
        'totalUsers',
        'activeWorkers',
        'jobStatusData',
        'applicationData',
        'topWorkers'
    ));
}

   public function exportPDF()
{
    // 1️⃣ Load jobs from your DB
    $jobs = Job::all();

    // 2️⃣ Render the Blade view
    $html = view('admin.reports.pdf', compact('jobs'))->render();

    // 3️⃣ Configure and load Dompdf
    $options = new Options();
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isRemoteEnabled', true);

    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    // 4️⃣ Stream the PDF to browser
    return $dompdf->stream('workbridge_report.pdf', ['Attachment' => false]);
}
}
