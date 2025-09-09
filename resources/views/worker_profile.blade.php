@extends('layouts.app')

@section('title', 'Worker Profile')

@section('content')
<h1>Your Profile</h1>
<form method="POST" action="{{ route('worker.updateProfile') }}" enctype="multipart/form-data">
    @csrf
    <label>Name:</label>
    <input type="text" name="name" value="{{ auth()->user()->name }}">
    <label>Upload Resume:</label>
    <input type="file" name="resume">
    <label>Skills (comma-separated):</label>
    <input type="text" name="skills" value="{{ implode(', ', auth()->user()->skills ?? []) }}">
    <button type="submit">Update Profile</button>
</form>
@endsection
