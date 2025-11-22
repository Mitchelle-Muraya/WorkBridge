@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h3 class="fw-bold text-primary mb-4">✅ Completed Jobs</h3>

    @if($completedJobs->isEmpty())
        <div class="alert alert-info text-center shadow-sm">
            You haven't completed any jobs yet.
        </div>
    @else
        <div class="row">
            @foreach($completedJobs as $job)
            <div class="col-md-6 mb-4">
                <div class="card border-0 shadow-sm p-3 rounded-4">
                    <div class="card-body">
                        <h5 class="fw-bold text-dark">{{ $job->title }}</h5>
                        <p class="text-muted small mb-1"><i class="bi bi-geo-alt"></i> {{ $job->location ?? 'N/A' }}</p>
                        <p class="text-secondary">{{ Str::limit($job->description, 100) }}</p>

                        <div class="d-flex align-items-center mb-3">
                            <img src="{{ asset('images/avatar-default.png') }}" alt="Worker" class="rounded-circle me-2" width="45" height="45">
                            <div>
                               <p class="mb-0 fw-semibold text-dark">
    {{ optional(optional($job->worker)->user)->name ?? 'N/A' }}
</p>

                                <small class="text-muted">Assigned Worker</small>
                            </div>
                        </div>

                        <hr>

                        <!-- Inline Review Form -->
                        <form method="POST" action="{{ route('reviews.store') }}">
                            @csrf
                            <input type="hidden" name="job_id" value="{{ $job->id }}">
                            <input type="hidden" name="worker_id" value="{{ $job->worker_id }}">

                            <label class="form-label fw-semibold mb-1">Your Rating</label>
                            <div class="rating d-flex flex-row-reverse justify-content-start mb-3">
                                @for ($i = 5; $i >= 1; $i--)
                                    <input type="radio" name="rating" id="star{{ $i }}-{{ $job->id }}" value="{{ $i }}" required>
                                    <label for="star{{ $i }}-{{ $job->id }}">★</label>
                                @endfor
                            </div>

                            <div class="mb-3">
                                <textarea name="comment" class="form-control rounded-3" rows="3"
                                    placeholder="Write your review here..."></textarea>
                            </div>

                            <button type="submit" class="btn btn-success w-100">
                                <i class="bi bi-send"></i> Submit Review
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
.rating {
    direction: rtl;
    display: inline-flex;
    font-size: 1.5rem;
}
.rating input {
    display: none;
}
.rating label {
    color: #ccc;
    cursor: pointer;
    transition: color 0.2s;
    margin-right: 3px;
}
.rating input:checked ~ label,
.rating label:hover,
.rating label:hover ~ label {
    color: #f6b93b;
}
.card {
    transition: 0.2s ease-in-out;
}
.card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 18px rgba(0,0,0,0.08);
}
</style>
@endsection
