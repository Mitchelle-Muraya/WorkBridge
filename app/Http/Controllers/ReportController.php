<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Job;
use App\Models\Application;
use App\Models\User;
use App\Models\Worker;
use Dompdf\Dompdf;
use Dompdf\Options;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReportExport;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // 1️⃣ Get filter values
        $start = $request->start_date ?? now()->subDays(30)->format('Y-m-d');
        $end   = $request->end_date   ?? now()->format('Y-m-d');
        $status = $request->status ?? '';
        $location = $request->location ?? '';
        $category = $request->category ?? '';

        // 2️⃣ Build dynamic query
        $query = Job::query();
        $query->whereBetween('created_at', [$start, $end]);

        if ($status) {
            $query->where('status', $status);
        }

        if ($location) {
            $query->where('location', 'LIKE', "%$location%");
        }

        if ($category) {
            $query->where('skills_required', 'LIKE', "%$category%");
        }

        // Filtered jobs
        $filteredJobs = $query->get();

        // 3️⃣ Summary cards (based on filtered jobs)
        $totalJobs = $filteredJobs->count();
        $totalApplications = Application::count();
        $totalUsers = User::count();
        $activeWorkers = Worker::whereNotNull('skills')->count();

        // 4️⃣ Job status breakdown (filtered)
        $jobStatusData = [
            'pending' => $query->clone()->where('status', 'pending')->count(),
            'in_progress' => $query->clone()->where('status', 'in_progress')->count(),
            'completed' => $query->clone()->where('status', 'completed')->count(),
        ];

        // 5️⃣ Application status breakdown
        $applicationData = [
            'pending' => Application::where('status', 'pending')->count(),
            'approved' => Application::where('status', 'approved')->count(),
            'rejected' => Application::where('status', 'rejected')->count(),
        ];

        // 6️⃣ Top workers
        $topWorkers = Worker::withAvg('reviews', 'rating')
            ->orderByDesc('reviews_avg_rating')
            ->take(5)
            ->get();

        // Return everything to view
        return view('admin.reports.index', compact(
            'start', 'end', 'status', 'location', 'category',
            'filteredJobs', 'jobStatusData', 'applicationData',
            'totalJobs', 'totalApplications', 'totalUsers',
            'activeWorkers', 'topWorkers'
        ));
    }


    public function exportPDF(Request $request)
    {
        // Re-build filtered query for export
        $query = Job::query();

        if ($request->start_date && $request->end_date) {
            $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->location) {
            $query->where('location', 'LIKE', "%{$request->location}%");
        }

        if ($request->category) {
            $query->where('skills_required', 'LIKE', "%{$request->category}%");
        }

        $jobs = $query->get();

        // PDF rendering
        $html = view('admin.reports.pdf', compact('jobs'))->render();

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->stream('workbridge_report.pdf', ['Attachment' => false]);
    }
}
