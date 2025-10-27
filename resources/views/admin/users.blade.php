@extends('layouts.admin')

@section('content')
<div class="container py-4">
    <h2 class="fw-bold text-primary mb-4"> Manage Users</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($users->count() > 0)
        <table class="table table-hover align-middle shadow-sm">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Joined</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ ucfirst($user->role ?? 'N/A') }}</td>
                        <td>
                            <span class="badge {{ $user->status == 'active' ? 'bg-success' : 'bg-secondary' }}">
                                {{ ucfirst($user->status ?? 'inactive') }}
                            </span>
                        </td>
                        <td>{{ $user->created_at->format('Y-m-d') }}</td>
                        <td class="d-flex gap-1">
                            @if($user->status !== 'active')
                                <form method="POST" action="{{ route('admin.users.activate', $user->id) }}">
                                    @csrf
                                    <button class="btn btn-sm btn-success">Activate</button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('admin.users.deactivate', $user->id) }}">
                                    @csrf
                                    <button class="btn btn-sm btn-warning">Deactivate</button>
                                </form>
                            @endif
                            <form method="POST" action="{{ route('admin.users.delete', $user->id) }}">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p class="text-muted">No users found in the system.</p>
    @endif
</div>
@endsection
