@extends('layouts.admin')

@section('content')
<style>
    /* Minimal black & white action styling */
    .action-btn {
        background: none;
        border: none;
        color: #000;
        font-size: 14px;
        padding: 0;
        cursor: pointer;
        text-decoration: underline;
    }

    .action-btn:hover {
        opacity: 0.7;
    }

    .icon-btn {
        background: none;
        border: none;
        cursor: pointer;
        padding: 0;
    }

    .icon-btn i {
        font-size: 18px;
        color: #000;
    }

    .icon-btn i:hover {
        transform: scale(1.1);
    }

    td, th {
        vertical-align: middle !important;
    }

    .icon-col {
        width: 40px;
        text-align: center;
    }
</style>

<div class="container py-4">
    <h2 class="fw-bold text-primary mb-4">Manage Jobs</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($jobs->count() > 0)
        <table class="table table-hover align-middle shadow-sm">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Client</th>
                    <th>Location</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th colspan="3" class="text-center">Actions</th>
                </tr>
            </thead>

            <tbody>
                @foreach($jobs as $job)
                    <tr>
                        <td>{{ $job->id }}</td>
                        <td>{{ $job->title }}</td>
                        <td>{{ $job->client->name ?? 'Unknown Client' }}</td>
                        <td>{{ $job->location }}</td>

                        <!-- Plain text status -->
                        <td>{{ ucfirst($job->status) }}</td>

                        <td>{{ $job->created_at->format('Y-m-d') }}</td>

                        <!-- VIEW -->
                        <td class="icon-col">
                            <a href="{{ route('admin.jobs.view', $job->id) }}" title="View" class="icon-btn">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>

                        <td>
                            @if($job->status == 'pending')
                                <!-- APPROVE -->
                                <form method="POST" action="{{ route('admin.jobs.approve', $job->id) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="action-btn">Approve</button>
                                </form>

                                <!-- REJECT -->
                                <form method="POST" action="{{ route('admin.jobs.reject', $job->id) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="action-btn">Reject</button>
                                </form>
                            @endif

                            @if($job->status == 'in_progress')
                                <!-- COMPLETE -->
                                <form method="POST" action="{{ route('admin.jobs.complete', $job->id) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="action-btn">Complete</button>
                                </form>
                            @endif
                        </td>

                        <!-- DELETE ICON -->
                        <td class="icon-col">
                            <form method="POST" action="{{ route('admin.jobs.delete', $job->id) }}">
                                @csrf @method('DELETE')
                                <button type="submit" class="icon-btn" title="Delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>

                    </tr>
                @endforeach
            </tbody>
        </table>

    @else
        <p class="text-muted">No jobs found in the database.</p>
    @endif
</div>
@endsection
