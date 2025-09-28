@extends('layouts.app')

@section('content')
<div class="container text-center">
    <h2>Welcome, {{ Auth::user()->name }}!</h2>
    <p>What would you like to do?</p>

    <form method="POST" action="{{ route('store.role') }}">
        @csrf
        <button type="submit" name="role" value="worker" class="btn btn-success m-2">
            👷 Become a Worker (Find Work)
        </button>
        <button type="submit" name="role" value="client" class="btn btn-primary m-2">
            📝 Become a Client (Post Jobs)
        </button>
    </form>
</div>
@endsection
