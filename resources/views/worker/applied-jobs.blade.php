@extends('layouts.app')

@section('content')
<style>
    body {
        background-color: #f6f9fc;
    }

    .page-header {
        display: flex;
        align-items: center;
        margin-bottom: 2rem;
    }

    .page-header h3 {
        font-weight: 700;
        color: #0099ff;
        margin-left: 10px;
    }

    .card-job {
        background: #fff;
        border-radius: 18px;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
        transition: all 0.25s ease;
        border: 1px solid #e3f2fd;
    }

    .card-job:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
    }

    .card-job .job-title {
        color: #111827;
        font-weight: 700;
        margin-bottom: 6px;
    }

    .card-job .location {
        color: #6b7280;
        font-size: 0.95rem;
        margin-bottom: 8px;
    }

    .badge-status {
        font-size: 0.78rem;
        font-weight: 600;
        border-radius: 6px;
        padding: 0.35em 0.6em;
        display: inline-block;
    }

    .badge-status.pending {
        background-color: #ffeb99;
        color: #7a5c00;
    }

    .badge-status.accepted {
        background-color: #d1fae5;
        color: #065f46;
    }

    .badge-status.rejected {
        background-color: #fee2e2;
        color: #991b1b;
    }

    .applied-date {
        color: #9ca3af;
        font-size: 0.85rem;
    }

    @media (max-width: 768px) {
        .page-header {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>

<div class="container py-5">
    <div class="page-header">
        <i class="bi bi-briefcase-fill text-primary fs-3"></i>
        <h3>My Jobs</h3>
    </div>

    @if($applications->isEmpty())
        <div class="alert alert-info border-0 shadow-sm rounded-3">
            You haven’t applied for any jobs yet.
        </div>
    @else
        <div class="d-flex flex-column gap-4">
            @foreach($applications as $app)
                <div class="card card-job px-4 py-3">
                    <h5 class="job-title">{{ $app->job->title }}</h5>
                    <p class="location">
                        <i class="bi bi-geo-alt-fill text-primary me-1"></i> {{ $app->job->location }}
                    </p>
                    <p class="mb-1">
                        <strong>Status:</strong>
                        <span class="badge-status {{ strtolower($app->status) }}">
                            {{ ucfirst($app->status) }}
                        </span>
                    </p>
                    <p class="applied-date mb-0">
                        <i class="bi bi-calendar-event me-1"></i>
                        Applied on {{ \Carbon\Carbon::parse($app->applied_at)->format('M d, Y h:i A') }}
                    </p>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
