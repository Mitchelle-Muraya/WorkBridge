@extends('layouts.app')

@section('title', 'Client Profile')

@section('content')
<h1>Your Profile</h1>
<form method="POST" action="{{ route('client.updateProfile') }}">
    @csrf
    <label>Name:</label>
    <input type="text" name="name" value="{{ auth()->user()->name }}">
    <label>Company Info:</label>
    <textarea name="company_info">{{ auth()->user()->company_info }}</textarea>
    <button type="submit">Update Profile</button>
</form>
@endsection
