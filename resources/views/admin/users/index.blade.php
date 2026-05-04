@extends('layouts.app')

@section('title', 'User Management')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h4 fw-bold mb-0"><i class="bi bi-people text-primary me-2"></i>User Management</h1>
        <p class="text-muted small mb-0">Manage users and their roles</p>
    </div>
    <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-primary btn-sm">
        <i class="bi bi-shield-lock me-1"></i>Manage Roles & Permissions
    </a>
</div>

{{-- Stats --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card card text-center py-3">
            <div class="fs-2 fw-bold text-dark">{{ $users->total() }}</div>
            <div class="small text-muted mt-1">Total Users</div>
        </div>
    </div>
    @foreach($roles as $role)
    <div class="col-6 col-md-3">
        <div class="stat-card card text-center py-3">
            <div class="fs-2 fw-bold text-primary">
                {{ \App\Models\User::where('role_id', $role->id)->count() }}
            </div>
            <div class="small text-muted mt-1">{{ $role->name }}s</div>
        </div>
    </div>
    @endforeach
    <div class="col-6 col-md-3">
        <div class="stat-card card text-center py-3">
            <div class="fs-2 fw-bold text-warning">
                {{ \App\Models\User::whereNull('role_id')->count() }}
            </div>
            <div class="small text-muted mt-1">No Role</div>
        </div>
    </div>
</div>

<div class="bg-white rounded-3 shadow-sm overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr class="bg-light border-bottom">
                    <th class="ps-4 text-muted fw-600 small" style="width:5%">#</th>
                    <th class="text-muted fw-600 small" style="width:25%">NAME</th>
                    <th class="text-muted fw-600 small" style="width:25%">EMAIL</th>
                    <th class="text-muted fw-600 small" style="width:15%">REQUESTS</th>
                    <th class="text-muted fw-600 small" style="width:15%">JOINED</th>
                    <th class="text-muted fw-600 small pe-4" style="width:15%">ROLE</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr class="align-middle border-bottom">
                    <td class="ps-4 text-muted small">
                        {{ ($users->currentPage() - 1) * $users->perPage() + $loop->iteration }}
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <span class="d-inline-flex align-items-center justify-content-center rounded-circle text-white fw-bold"
                                  style="width:34px;height:34px;font-size:.8rem;flex-shrink:0;
                                         background:{{ $user->id === auth()->id() ? '#0d6efd' : '#6c757d' }}">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </span>
                            <div>
                                <div class="fw-500 small">
                                    {{ $user->name }}
                                    @if($user->id === auth()->id())
                                        <span class="badge bg-primary ms-1" style="font-size:.65rem">You</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="text-muted small">{{ $user->email }}</td>
                    <td>
                        <span class="badge bg-light text-dark border">
                            {{ $user->service_requests_count }} {{ Str::plural('request', $user->service_requests_count) }}
                        </span>
                    </td>
                    <td class="text-muted small">
                        {{ $user->created_at->format('d M Y') }}
                    </td>
                    <td class="pe-4">
                        {{-- Inline role assignment form --}}
                        <form action="{{ route('admin.users.updateRole', $user) }}" method="POST"
                              class="d-flex align-items-center gap-2">
                            @csrf
                            @method('PATCH')
                            <select name="role_id"
                                    class="form-select form-select-sm"
                                    style="min-width:110px"
                                    onchange="this.form.submit()"
                                    {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                                <option value="">— No Role —</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}"
                                            {{ $user->role_id == $role->id ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                            @if($user->id === auth()->id())
                                <i class="bi bi-lock text-muted" title="Cannot change your own role"></i>
                            @endif
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($users->hasPages())
        <div class="d-flex justify-content-between align-items-center px-4 py-3 border-top bg-light">
            <div class="small text-muted">
                Showing {{ $users->firstItem() }}–{{ $users->lastItem() }} of {{ $users->total() }} users
            </div>
            {{ $users->links('pagination::bootstrap-5') }}
        </div>
    @else
        <div class="px-4 py-2 border-top bg-light small text-muted">
            {{ $users->total() }} {{ Str::plural('user', $users->total()) }}
        </div>
    @endif
</div>

@endsection
