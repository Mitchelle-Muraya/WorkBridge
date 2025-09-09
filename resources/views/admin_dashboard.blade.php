@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<h1>Admin Dashboard</h1>
<h2>Assign Roles</h2>
<ul>
@foreach($users as $user)
    <li>
        {{ $user->name }} - Current Role: {{ $user->role }}
        <form method="POST" action="{{ route('admin.assignRole', $user->id) }}">
            @csrf
            <select name="role">
                <option value="worker">Worker</option>
                <option value="client">Client</option>
                <option value="admin">Admin</option>
            </select>
            <button type="submit">Assign</button>
        </form>
    </li>
@endforeach
</ul>
@endsection
