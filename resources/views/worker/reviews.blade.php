@extends('layouts.app')

@section('content')

<div class="container mt-4">
    <h3 class="fw-bold text-primary mb-4">⭐ Reviews You've Received</h3>

    @if($reviews->isEmpty())
        <div class="alert alert-info text-center">No reviews yet.</div>
    @else
        @foreach($reviews as $review)
            <div class="card shadow-sm p-3 rounded-4 mb-3">
                <h5 class="fw-bold">{{ $review->job->title }}</h5>

                <p class="text-warning mb-1">
                    ⭐ {{ $review->rating }} / 5
                </p>

                <p>{{ $review->comment }}</p>

                <small class="text-muted">
                    Client: {{ $review->client->name }}
                </small>
            </div>
        @endforeach
    @endif
</div>

@endsection
