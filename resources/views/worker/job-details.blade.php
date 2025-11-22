@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="mx-auto p-4 shadow-sm bg-white rounded-3" style="max-width: 700px;">
        <div class="d-flex justify-content-between align-items-start mb-3">
            <h4 class="fw-bold mb-0">{{ $job->title }}</h4>
            <span class="badge bg-success text-white px-3 py-2">{{ ucfirst($job->status) }}</span>
        </div>

        <p class="text-muted mb-2">
            <i class="bi bi-geo-alt text-danger"></i> {{ ucfirst($job->location) }}
        </p>

        <p class="mb-3" style="line-height: 1.6;">{{ $job->description }}</p>

        @if($job->skills_required)
            <p class="fw-semibold mb-2">🛠️ Skills Required:
                <span class="text-secondary">{{ $job->skills_required }}</span>
            </p>
        @endif

        @if($job->budget)
            <p class="fw-semibold mb-2">
                💰 Budget: <span class="text-success">KSH {{ number_format($job->budget) }}</span>
            </p>
        @endif

        @if($job->deadline)
            <p class="fw-semibold mb-4">
                🗓️ Deadline: {{ \Carbon\Carbon::parse($job->deadline)->format('d M Y') }}
            </p>
        @endif

        <form action="{{ route('apply.job', $job->id) }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-primary w-100 py-2">Apply for This Job</button>
        </form>

        <div class="text-center mt-4">
            <a href="{{ route('worker.findJobs') }}" class="text-decoration-none text-secondary small">
                ← Back to Available Jobs
            </a>
        </div>
    </div>
</div>

<style>
    body {
        background-color: #f8f9fa;
    }
    .container {
        animation: fadeIn 0.4s ease-in-out;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endsection
