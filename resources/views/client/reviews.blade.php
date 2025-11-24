@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h3 class="fw-bold text-primary mb-4">📢 Reviews You've Received</h3>

    @if($reviews->isEmpty())
        <div class="alert alert-info text-center shadow-sm">
            You have not received any reviews yet.
        </div>
    @else
        @foreach($reviews as $review)
            <div class="card shadow-sm mb-3 p-3 rounded-4">

                <h5 class="fw-bold">{{ $review->job->title }}</h5>

                <p class="text-warning mb-1">
                    ⭐ {{ $review->rating }} / 5
                </p>

                <p class="text-muted">{{ $review->comment }}</p>

                <small class="text-secondary">
                    Worker: {{ $review->worker->user->name }}
                </small>
            </div>
        @endforeach
    @endif
</div>
@endsection
