@extends('layouts.auth')

@section('title', 'Register')

@section('content')
<h1>Create Account</h1>
<form method="POST" action="{{ route('register') }}">
    @csrf
    <label>Name:</label>
    <input type="text" name="name" required>
    <label>Email:</label>
    <input type="email" name="email" required>
    <label>Password:</label>
    <input type="password" name="password" required>
    <button type="submit">Register</button>
</form>
<a href="{{ route('login') }}">Login</a>
@endsection
