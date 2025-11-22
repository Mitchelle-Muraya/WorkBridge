@extends('layouts.admin')


@section('content')
<div class="container-fluid px-4 py-4">
  <h2 class="fw-bold text-primary mb-4">
    <i class="bi bi-bar-chart-line me-2"></i> Admin Reports Dashboard
  </h2>
<div class="d-flex justify-content-between align-items-center mb-4">
 @isset($start)
<form method="GET" class="d-flex gap-2">
    <input type="date" name="start_date" value="{{ $start ?? '' }}" class="form-control form-control-sm">
    <input type="date" name="end_date" value="{{ $end ?? '' }}" class="form-control form-control-sm">
    <button class="btn btn-primary btn-sm"><i class="bi bi-funnel"></i> Filter</button>
</form>
@endisset


  <div class="btn-group">
    <a href="{{ route('admin.reports.pdf') }}" class="btn btn-danger btn-sm"><i class="bi bi-file-earmark-pdf"></i> Export PDF</a>
    <a href="{{ route('admin.reports.excel') }}" class="btn btn-success btn-sm"><i class="bi bi-file-earmark-excel"></i> Export Excel</a>
  </div>
</div>

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


  <!-- ================= CHARTS ================= -->
  <div class="row g-4">
    <div class="col-md-6">
      <div class="card shadow-sm border-0 rounded-4 p-4">
        <h6 class="fw-semibold text-primary mb-3">
          <i class="bi bi-briefcase-fill me-1"></i> Job Status Overview
        </h6>
        <canvas id="jobStatusChart" height="250"></canvas>
      </div>
    </div>

    <div class="card p-4">
  <h6 class="text-primary fw-bold mb-3">Applications Breakdown</h6>
  <canvas id="applicationsChart" height="120"></canvas>
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


  <div class="row g-4 mt-4">
    <div class="col-md-7">
      <div class="card shadow-sm border-0 rounded-4 p-4">
        <h6 class="fw-semibold text-primary mb-3">
          <i class="bi bi-calendar-week me-1"></i> Monthly Jobs Trend
        </h6>
        <canvas id="monthlyJobsChart" height="260"></canvas>
      </div>
    </div>

    <div class="col-md-5">
      <div class="card shadow-sm border-0 rounded-4 p-4">
        <h6 class="fw-semibold text-primary mb-3">
          <i class="bi bi-star-fill me-1"></i> Top Rated Workers
        </h6>
        @if($topWorkers->isEmpty())
          <p class="text-muted">No ratings yet.</p>
        @else
          <table class="table table-sm align-middle">
            <thead>
              <tr>
                <th>Worker</th>
                <th>Avg Rating</th>
                <th>Reviews</th>
              </tr>
            </thead>
            <tbody>
              @foreach($topWorkers as $worker)
                <tr>
                  <td>{{ $worker->worker->name ?? 'Unknown' }}</td>
                  <td>
                    <span class="badge bg-success">
                      <i class="bi bi-star-fill"></i> {{ number_format($worker->avg_rating, 1) }}
                    </span>
                  </td>
                  <td>{{ $worker->total_reviews }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        @endif
      </div>
    </div>
  </div>

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


