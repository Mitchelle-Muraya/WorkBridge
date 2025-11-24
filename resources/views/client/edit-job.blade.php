@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h2 class="fw-bold mb-4"><i class="bi bi-pencil-square"></i> Edit Job</h2>

    <form action="{{ route('client.job.update', $job->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label fw-semibold">Job Title</label>
            <input type="text" name="title" class="form-control" value="{{ $job->title }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Description</label>
            <textarea name="description" class="form-control" rows="5" required>{{ $job->description }}</textarea>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Budget (KSH)</label>
            <input type="number" name="budget" class="form-control" value="{{ $job->budget }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Deadline</label>
            <input type="date" name="deadline" class="form-control" value="{{ $job->deadline }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Location</label>
            <input type="text" name="location" class="form-control" value="{{ $job->location }}" required>
        </div>

        <button class="btn btn-primary">Save Changes</button>
        <a href="{{ route('client.my-jobs') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
