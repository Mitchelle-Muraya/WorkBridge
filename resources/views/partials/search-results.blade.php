@if($jobs->isEmpty())
  <div class="text-center text-muted mt-3">No jobs found.</div>
@else
  @foreach($jobs as $job)
    <div class="card job-card border-0 shadow-sm mb-3">
      <div class="card-body">
        <h5 class="fw-bold text-primary">{{ $job->title }}</h5>
        <p class="text-muted small mb-1">{{ $job->location }}</p>
        <p>{{ Str::limit($job->description, 100) }}</p>
        <form class="apply-form" action="{{ route('apply.job', $job->id) }}" method="POST">
          @csrf
          <button type="submit" class="btn btn-success w-100 mt-2">
            Apply
          </button>
        </form>
      </div>
    </div>
  @endforeach
@endif
