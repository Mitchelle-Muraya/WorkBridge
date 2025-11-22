@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h3 class="mb-4 fw-bold text-primary">⭐ My Reviews</h3>

    @if($reviews->isEmpty())
        <p class="text-muted">You haven’t received any reviews yet.</p>
    @else
        @foreach($reviews as $review)
            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <h5 class="fw-bold text-dark">{{ $review->client->name ?? 'Anonymous' }}</h5>
                    <p class="mb-1 text-warning">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="bi {{ $i <= $review->rating ? 'bi-star-fill text-warning' : 'bi-star text-secondary' }}"></i>
                        @endfor
                    </p>
                    <p class="text-muted small">{{ $review->created_at->diffForHumans() }}</p>
                    <p>{{ $review->comment }}</p>
                </div>
            </div>
        @endforeach
    @endif
</div>
@endsection
