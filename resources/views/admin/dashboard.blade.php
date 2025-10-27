@extends('layouts.admin')

@section('content')
<div class="p-4">
  <h4 class="text-primary fw-bold"><i class="bi bi-speedometer2 me-2"></i>Admin Dashboard</h4>

  <div class="row g-4 mt-3">
    <div class="col-md-4">
      <div class="card shadow-sm border-0 rounded-4 p-3 text-center bg-light">
        <h6>Total Users</h6>
        <h2 class="fw-bold text-primary">{{ $totalUsers }}</h2>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card shadow-sm border-0 rounded-4 p-3 text-center bg-light">
        <h6>Total Jobs</h6>
        <h2 class="fw-bold text-success">{{ $totalJobs }}</h2>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card shadow-sm border-0 rounded-4 p-3 text-center bg-light">
        <h6>Total Applications</h6>
        <h2 class="fw-bold text-info">{{ $totalApplications }}</h2>
      </div>
    </div>
  </div>

  <p class="text-muted mt-4">
    Welcome to your admin dashboard. Use the sidebar to manage users, jobs, and reports.
  </p>
</div>
@endsection
