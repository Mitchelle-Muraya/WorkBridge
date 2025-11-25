@extends('layouts.admin')

@section('content')
<style>
    /* Black & white minimal style */
    .action-btn {
        background: none;
        border: none;
        color: #000;
        font-size: 14px;
        padding: 6px 8px;
        cursor: pointer;
    }

    .action-btn:hover {
        text-decoration: underline;
    }

    .delete-icon {
        background: none;
        border: none;
        font-size: 18px;
        cursor: pointer;
        color: #000;
        padding: 0;
        margin-left: 10px;
    }

    .delete-icon:hover {
        transform: scale(1.1);
    }

    td, th {
        vertical-align: middle !important;
    }

    .icon-col {
        width: 40px;
        text-align: center;
    }
</style>

<div class="container py-4">
    <h2 class="fw-bold text-primary mb-4">Manage Users</h2>

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
                    <th colspan="2" class="text-center">Actions</th>
                </tr>
            </thead>

            <tbody>
                @foreach($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ ucfirst($user->role ?? 'N/A') }}</td>

                        <!-- Plain text status -->
                        <td>{{ ucfirst($user->status ?? 'Inactive') }}</td>

                        <td>{{ $user->created_at->format('Y-m-d') }}</td>

                        <td>
                            <!-- Activate / Deactivate in black text -->
                            @if($user->status !== 'active')
                                <form method="POST" action="{{ route('admin.users.activate', $user->id) }}">
                                    @csrf
                                    <button class="action-btn">Activate</button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('admin.users.deactivate', $user->id) }}">
                                    @csrf
                                    <button class="action-btn">Deactivate</button>
                                </form>
                            @endif
                        </td>

                        <td class="icon-col">
                            <!-- Delete icon only (aligned perfectly) -->
                            <form method="POST" action="{{ route('admin.users.delete', $user->id) }}">
                                @csrf @method('DELETE')
                                <button type="submit" class="icon-btn" title="Delete">
                                    <i class="bi bi-trash"></i>
                                </button>
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
