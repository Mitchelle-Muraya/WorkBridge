@extends('layouts.auth')

@section('title', 'Login')

@section('content')
<h1>Login to WorkBridge</h1>
<form method="POST" action="{{ route('login') }}">
    @csrf
    <label>Email:</label>
    <input type="email" name="email" required>
    <label>Password:</label>
    <input type="password" name="password" required>
    <button type="submit">Login</button>
</form>
<a href="{{ route('register') }}">Register</a>
@endsection
