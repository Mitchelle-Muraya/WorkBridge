@extends('layouts.admin')

@section('content')
<div class="container py-4">
    <h3 class="fw-bold">Job Details</h3>

    <div class="card p-4">
        <h4>{{ $job->title }}</h4>
        <p><strong>Description:</strong> {{ $job->description }}</p>
        <p><strong>Budget:</strong> KSH {{ number_format($job->budget) }}</p>
        <p><strong>Location:</strong> {{ $job->location }}</p>
        <p><strong>Status:</strong> {{ ucfirst($job->status) }}</p>

        <hr>

        <h5>Client Details</h5>
        <p>{{ $job->client->name }} ({{ $job->client->email }})</p>

        <hr>

        <h5>Applications</h5>
        @foreach($job->applications as $app)
            <p>{{ $app->user->name }} applied on {{ $app->applied_at }}</p>
        @endforeach
    </div>
</div>
@endsection
