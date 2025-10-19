@extends('layouts.worker')

@section('content')
<div class="content">
  <h2 class="fw-bold mb-4"><i class="bi bi-gear"></i> Settings</h2>

  <div class="job-card mb-4">
    <p class="text-muted">Here you can update your account settings, change password, and manage preferences.</p>

    <form action="#" method="POST">
      @csrf
      <div class="mb-3">
        <label class="form-label fw-semibold">Email Notifications</label>
        <select class="form-select">
          <option>Enabled</option>
          <option>Disabled</option>
        </select>
      </div>

      <div class="mb-3">
        <label class="form-label fw-semibold">Dark Mode</label>
        <select class="form-select">
          <option>Enabled</option>
          <option>Disabled</option>
        </select>
      </div>

      <button class="btn btn-primary mt-3">Save Changes</button>
    </form>
  </div>
</div>
@endsection
