@extends('layouts.app')

@section('title', 'Client Dashboard')

@section('content')
<h1>Welcome, {{ auth()->user()->name }}</h1>

<h2>Recommended Workers for Your Jobs</h2>
<ul>
@foreach($recommendedWorkers as $worker)
    <li>
        <strong>{{ $worker->name }}</strong><br>
        Skills: {{ implode(', ', $worker->extracted_skills) }}<br>
        <a href="{{ route('client.viewWorker', $worker->id) }}">View Profile</a>
    </li>
@endforeach
</ul>
@endsection
