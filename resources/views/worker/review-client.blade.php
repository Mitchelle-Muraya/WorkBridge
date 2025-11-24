@extends('layouts.app')

@section('content')

<style>
    .review-container {
        max-width: 700px;
        margin: 40px auto;
        background: white;
        padding: 35px;
        border-radius: 18px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }
    .star { font-size: 35px; cursor: pointer; color: #ccc; }
    .star.active { color: #ffc107; }
</style>

<div class="review-container">

    <h4 class="fw-bold mb-3">
        ⭐ Rate Client - {{ $job->title }}
        <br>
        <small class="text-muted">Client: {{ $client->name }}</small>
    </h4>

    <form action="{{ route('worker.review.store') }}" method="POST">
        @csrf

        <input type="hidden" name="job_id" value="{{ $job->id }}">
        <input type="hidden" name="client_id" value="{{ $client->id }}">
        <input type="hidden" name="rating" id="ratingValue">

        <label class="fw-bold mb-1">Rating</label>
        <div class="star-rating mb-3">
            @for ($i = 1; $i <= 5; $i++)
                <i class="star bi bi-star-fill" data-value="{{ $i }}"></i>
            @endfor
        </div>

        <label class="fw-bold mb-1">Review</label>
        <textarea name="review" class="form-control mb-3" rows="4" required></textarea>

        <button class="btn btn-success w-100">Submit Review</button>
    </form>
</div>

<script>
    const stars = document.querySelectorAll(".star");
    const val = document.getElementById("ratingValue");
    stars.forEach(star => star.onclick = () => {
        val.value = star.dataset.value;
        stars.forEach(s => s.classList.remove("active"));
        for (let i = 0; i < star.dataset.value; i++) stars[i].classList.add("active");
    });
</script>

@endsection
