@extends('layouts.app')

@section('title', 'Worker Dashboard')

@section('content')
<h1>Welcome, {{ auth()->user()->name }}</h1>

<h2>Recommended Jobs</h2>
<ul>
@foreach($recommendedJobs as $job)
    <li>
        <strong>{{ $job->title }}</strong><br>
        Skills: {{ implode(', ', $job->extracted_skills) }}<br>
        <a href="{{ route('worker.apply', $job->id) }}">Apply</a>
    </li>
@endforeach
</ul>
@endsection
