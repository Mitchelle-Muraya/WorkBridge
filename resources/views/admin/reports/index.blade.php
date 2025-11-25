@php
    $status = $status ?? '';
    $location = $location ?? '';
    $category = $category ?? '';
@endphp

@extends('layouts.admin')


@section('content')
<div class="container-fluid px-4 py-4">

    <h2 class="fw-bold text-primary mb-4">
        <i class="bi bi-bar-chart-line me-2"></i> Admin Reports Dashboard
    </h2>

    <!-- EXPORT BUTTONS -->
    <div class="d-flex justify-content-end mb-3">
        <div class="btn-group">
            <a href="{{ route('admin.reports.pdf', request()->all()) }}" class="btn btn-danger btn-sm">
                <i class="bi bi-file-earmark-pdf"></i> Export PDF
            </a>
            <a href="{{ route('admin.reports.excel', request()->all()) }}" class="btn btn-success btn-sm">
                <i class="bi bi-file-earmark-excel"></i> Export Excel
            </a>
        </div>
    </div>

    <!-- FILTER FORM -->
    <form method="GET" class="row g-3 mb-4">
        <div class="col-md-3">
            <label class="form-label">Start Date</label>
            <input type="date" name="start_date" class="form-control" value="{{ $start }}">
        </div>

        <div class="col-md-3">
            <label class="form-label">End Date</label>
            <input type="date" name="end_date" class="form-control" value="{{ $end }}">
        </div>

        <div class="col-md-2">
            <label class="form-label">Job Status</label>
            <select name="status" class="form-select">
                <option value="">All</option>
                <option value="pending" {{ $status=='pending'?'selected':'' }}>Pending</option>
                <option value="in_progress" {{ $status=='in_progress'?'selected':'' }}>In Progress</option>
                <option value="completed" {{ $status=='completed'?'selected':'' }}>Completed</option>
            </select>
        </div>

        <div class="col-md-2">
            <label class="form-label">Location</label>
            <input type="text" name="location" class="form-control" value="{{ $location }}" placeholder="e.g Nairobi">
        </div>

        <div class="col-md-2">
            <label class="form-label">Category / Skill</label>
            <input type="text" name="category" class="form-control" value="{{ $category }}" placeholder="e.g Plumbing">
        </div>

        <div class="col-md-12">
            <button class="btn btn-primary mt-2"><i class="bi bi-funnel"></i> Apply Filters</button>
        </div>
    </form>


  <!-- ================= SUMMARY CARDS ================= -->
  <!-- ================= SUMMARY CARDS (Dark Mode Enhanced) ================= -->
<div class="row g-4 mb-4">

  <!-- 🟦 Total Jobs -->
  <div class="col-md-3">
    <div class="card border-0 shadow-sm rounded-4 p-3 text-center"
         style="background: linear-gradient(145deg, #1b213a, #2b324f); color: #fff; border: 1px solid #2f354a;">
      <h6 class="fw-semibold text-info mb-1">Total Jobs</h6>
      <h2 class="fw-bold">{{ array_sum($jobStatusData) }}</h2>

    </div>
  </div>

  <!-- 🟢 Applications -->
  <div class="col-md-3">
    <div class="card border-0 shadow-sm rounded-4 p-3 text-center"
         style="background: linear-gradient(145deg, #1b213a, #2b324f); color: #fff; border: 1px solid #2f354a;">
      <h6 class="fw-semibold text-primary mb-1">Applications</h6>
      <h2 class="fw-bold text-primary">{{ array_sum($applicationData) }}</h2>

    </div>
  </div>

  <!-- 🟩 Total Users -->
  <div class="col-md-3">
    <div class="card border-0 shadow-sm rounded-4 p-3 text-center"
         style="background: linear-gradient(145deg, #1b213a, #2b324f); color: #fff; border: 1px solid #2f354a;">
      <h6 class="fw-semibold text-success mb-1">Total Users</h6>
<h2 class="fw-bold text-success">{{ $totalUsers ?? 0 }}</h2>
    </div>
  </div>

  <!-- 🟨 Active Workers -->
  <div class="col-md-3">
    <div class="card border-0 shadow-sm rounded-4 p-3 text-center"
         style="background: linear-gradient(145deg, #1b213a, #2b324f); color: #fff; border: 1px solid #2f354a;">
      <h6 class="fw-semibold text-warning mb-1">Active Workers</h6>
      <h2 class="fw-bold text-light mb-0">{{ $userStats['worker'] ?? 0 }}</h2>
    </div>
  </div>

</div>

{{-- FILTERED JOB RESULTS TABLE --}}
@if(isset($filteredJobs) && $filteredJobs->count() > 0)

<div class="card shadow-sm mt-4">
    <div class="card-header bg-white">
        <h5 class="mb-0 fw-bold">Filtered Jobs ({{ $filteredJobs->count() }})</h5>
    </div>

    <div class="card-body p-0">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Client</th>
                    <th>Location</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th></th>
                </tr>
            </thead>

            <tbody>
                @foreach($filteredJobs as $job)
                <tr>
                    <td>{{ $job->id }}</td>
                    <td>{{ $job->title }}</td>
                    <td>{{ $job->client->name ?? 'Unknown' }}</td>
                    <td>{{ $job->location }}</td>
                    <td>{{ ucfirst($job->status) }}</td>
                    <td>{{ $job->created_at->format('Y-m-d') }}</td>

                    <td>
                        <a
                           href="{{ route('admin.jobs.view', $job->id) }}"
                           class="text-dark"
                           title="View Job">
                           <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@else
<div class="text-center text-muted mt-4">
    No jobs match the selected filters.
</div>
@endif


  <!-- ================= CHARTS ================= -->
  <!-- ================= CHARTS (SIDE BY SIDE) ================= -->
<div class="row g-4 mt-4">

    <!-- LEFT: Job Status Overview -->
    <div class="col-md-6">
        <div class="card shadow-sm border-0 rounded-4 p-4 h-100">
            <h6 class="fw-semibold text-primary mb-3">
                <i class="bi bi-pie-chart-fill me-1"></i> Job Status Overview
            </h6>
            <canvas id="jobStatusChart" height="260"></canvas>
        </div>
    </div>

    <!-- RIGHT: Applications Breakdown -->
    <div class="col-md-6">
        <div class="card shadow-sm border-0 rounded-4 p-4 h-100">
            <h6 class="fw-semibold text-primary mb-3">
                <i class="bi bi-clipboard-data me-1"></i> Applications Breakdown
            </h6>
            <canvas id="applicationsChart" height="260"></canvas>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const applicationCounts = @json($applicationData);

new Chart(document.getElementById('applicationsChart'), {
    type: 'doughnut',
    data: {
        labels: ['Pending', 'Approved', 'Rejected'],
        datasets: [{
            label: 'Applications',
            data: [
  $applicationData['pending'],
  $applicationData['approved'],
  $applicationData['rejected']
],

            backgroundColor: ['#00B3FF', '#00C9A7', '#FFB703'],
        }]
    },
    options: {
        plugins: {
            legend: { position: 'bottom' }
        }
    }
});
</script>



  <footer class="text-center mt-5 text-muted">
    <small>© {{ date('Y') }} WorkBridge — Smart Job Matching Analytics Dashboard</small>
  </footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // JOB STATUS CHART
    const jobStatusCtx = document.getElementById('jobStatusChart');
    if (jobStatusCtx) {
        new Chart(jobStatusCtx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode(array_keys($jobStatusData)) !!},
                datasets: [{
                    data: {!! json_encode(array_values($jobStatusData)) !!},
                    backgroundColor: ['#007bff', '#28a745', '#ffc107', '#dc3545'],
                }]
            },
            options: {
                plugins: {
                    title: {
                        display: true,
                        text: 'Job Status Overview'
                    }
                }
            }
        });
    }

    // APPLICATIONS CHART
    const appCtx = document.getElementById('applicationsChart');
    if (appCtx) {
        new Chart(appCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode(array_keys($applicationData)) !!},
                datasets: [{
                    label: 'Applications by Status',
                    data: {!! json_encode(array_values($applicationData)) !!},
                    backgroundColor: ['#36a2eb', '#ffce56', '#4bc0c0', '#9966ff']
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }
</script>

@endsection


