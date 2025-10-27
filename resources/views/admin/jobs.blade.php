@extends('layouts.admin')

@section('content')
<div class="container py-4">
    <h2 class="fw-bold text-primary mb-4"> Manage Jobs</h2>

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
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($jobs as $job)
                    <tr>
                        <td>{{ $job->id }}</td>
                        <td>{{ $job->title }}</td>
                        <td>{{ $job->client->name ?? 'Unknown Client' }}</td>
                        <td>{{ $job->location ?? 'N/A' }}</td>
                        <td>
                            <span class="badge
                                @if($job->status == 'pending') bg-warning
                                @elseif($job->status == 'in_progress') bg-info
                                @elseif($job->status == 'completed') bg-success
                                @else bg-secondary @endif">
                                {{ ucfirst($job->status) }}
                            </span>
                        </td>
                        <td>{{ $job->created_at->format('Y-m-d') }}</td>
                        <td class="d-flex gap-1">
                            @if($job->status == 'pending')
                                <form method="POST" action="{{ route('admin.jobs.approve', $job->id) }}">
                                    @csrf
                                    <button class="btn btn-sm btn-primary">Approve</button>
                                </form>
                            @elseif($job->status == 'in_progress')
                                <form method="POST" action="{{ route('admin.jobs.complete', $job->id) }}">
                                    @csrf
                                    <button class="btn btn-sm btn-success">Complete</button>
                                </form>
                            @endif
                            <form method="POST" action="{{ route('admin.jobs.delete', $job->id) }}">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger">Delete</button>
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
