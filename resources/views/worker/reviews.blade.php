@extends('layouts.worker')

@section('content')
<div class="content">
  <h2 class="fw-bold mb-4"><i class="bi bi-star"></i> My Reviews</h2>

  @forelse($reviews as $review)
    <div class="job-card mb-4">
      <h5 class="fw-semibold">{{ $review->job->title ?? 'Unknown Job' }}</h5>
      <p class="text-muted small mb-1">
        <strong>From:</strong> {{ $review->client->name ?? 'Client Deleted' }}
      </p>
      <p class="mb-1">
        <strong>Rating:</strong> ⭐ {{ $review->rating }}/5
      </p>
      <p class="text-muted">{{ $review->comment }}</p>
    </div>
  @empty
    <div class="alert alert-info text-center">
      <i class="bi bi-info-circle"></i> No reviews yet.
    </div>
  @endforelse
</div>
@endsection
