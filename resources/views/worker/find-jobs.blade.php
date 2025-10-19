@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
  <h3 class="mb-4 fw-bold text-primary">💼 Available Jobs</h3>

  <!-- Search Bar -->
  <form action="{{ route('worker.findJobs') }}" method="GET" class="d-flex mb-4" style="max-width: 500px;">
    <input type="text" name="query" class="form-control me-2" placeholder="Search jobs, e.g., plumber, electrician...">
    <button class="btn btn-primary"><i class="fas fa-search"></i></button>
  </form>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif
  @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
  @endif

  <!-- Job Listings -->
  @if($jobs->isEmpty())
    <div class="text-center mt-5 text-muted">
      <i class="fas fa-briefcase fa-3x mb-3"></i>
      <p>No available jobs right now. Check back later!</p>
    </div>
  @else
    <div class="row g-4">
      @foreach($jobs as $job)
      <div class="col-md-6 col-lg-4">
        <div class="card job-card border-0 shadow-sm h-100">
          <div class="card-body d-flex flex-column justify-content-between">
            <div>
              <h5 class="fw-bold text-primary">{{ $job->title }}</h5>
              <p class="text-muted small mb-1">
                <i class="fas fa-layer-group me-1"></i> {{ $job->category }}
              </p>
              <p class="text-muted small mb-1">
                <i class="fas fa-map-marker-alt me-1"></i> {{ $job->location }}
              </p>
              <p class="small mt-2">
                <span class="badge bg-light text-dark">Budget: Ksh {{ number_format($job->budget) }}</span><br>
                <span class="badge bg-light text-dark">Deadline: {{ \Carbon\Carbon::parse($job->deadline)->format('M d, Y') }}</span>
              </p>
            </div>

            <form action="{{ route('apply.job', $job->id) }}" method="POST" class="mt-3">
              @csrf
              <textarea name="cover_letter" class="form-control mb-2" rows="2" placeholder="Optional cover letter..."></textarea>
              <button type="submit" class="btn btn-success w-100">
                <i class="fas fa-paper-plane me-1"></i> Apply
              </button>
            </form>
          </div>
        </div>
      </div>
      @endforeach
    </div>
  @endif
</div>

<style>
  body {
    background-color: var(--bg-color);
    color: var(--text-color);
  }

  .job-card {
    border-radius: 16px;
    background-color: var(--card-bg);
    transition: transform 0.2s ease, box-shadow 0.3s ease;
  }

  .job-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
  }

  .form-control {
    background-color: var(--input-bg);
    color: var(--text-color);
    border: 1px solid #ccc;
  }

  .btn-success {
    border-radius: 10px;
    font-weight: 600;
  }

  [data-theme="dark"] .btn-success {
    background-color: #45a29e;
    border-color: #45a29e;
  }
</style>
@endsection
