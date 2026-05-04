@extends('layouts.app')

@section('title', 'Roles & Permissions')

@php
$permissionMeta = [
    'create_request' => [
        'label'  => 'Create Request',
        'icon'   => 'bi-plus-circle',
        'color'  => 'success',
        'desc'   => 'Can submit new service requests into the system.',
    ],
    'view_request' => [
        'label'  => 'View Requests',
        'icon'   => 'bi-eye',
        'color'  => 'info',
        'desc'   => 'Can view the list and details of service requests. Users see only their own; Admins see all.',
    ],
    'edit_request' => [
        'label'  => 'Edit Requests',
        'icon'   => 'bi-pencil',
        'color'  => 'warning',
        'desc'   => 'Can edit the title, description, status, and attachment of any request. Typically Admin only.',
    ],
    'delete_request' => [
        'label'  => 'Delete Requests',
        'icon'   => 'bi-trash',
        'color'  => 'danger',
        'desc'   => 'Can soft-delete (move to trash) a request. The request is not permanently removed and can be restored.',
    ],
    'view_trash' => [
        'label'  => 'View Trash',
        'icon'   => 'bi-trash2',
        'color'  => 'secondary',
        'desc'   => 'Can access the Trash page and see all soft-deleted requests.',
    ],
    'restore_request' => [
        'label'  => 'Restore Requests',
        'icon'   => 'bi-arrow-counterclockwise',
        'color'  => 'primary',
        'desc'   => 'Can restore a previously deleted request back to the active list.',
    ],
    'force_delete_request' => [
        'label'  => 'Force Delete',
        'icon'   => 'bi-x-octagon',
        'color'  => 'dark',
        'desc'   => 'Can permanently delete a request from trash. This action is irreversible and also removes any attached files.',
    ],
    'manage_users' => [
        'label'  => 'Manage Users',
        'icon'   => 'bi-people',
        'color'  => 'primary',
        'desc'   => 'Can access the Admin Panel: view all users, assign roles, and manage role permissions.',
    ],
];
@endphp

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h4 fw-bold mb-0">
            <i class="bi bi-shield-lock text-primary me-2"></i>Roles & Permissions
        </h1>
        <p class="text-muted small mb-0">Create roles and control what each role can do</p>
    </div>
    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-people me-1"></i>Manage Users
    </a>
</div>

{{-- ── Permission Reference ──────────────────────────────── --}}
<div class="bg-white rounded-3 shadow-sm p-4 mb-4">
    <h6 class="fw-bold mb-3 text-uppercase text-muted" style="font-size:.72rem;letter-spacing:.07em">
        <i class="bi bi-info-circle me-1"></i>Permission Reference
    </h6>
    <div class="row g-2">
        @foreach($permissions as $permission)
            @php $meta = $permissionMeta[$permission->name] ?? ['label' => $permission->name, 'icon' => 'bi-key', 'color' => 'secondary', 'desc' => '']; @endphp
            <div class="col-md-6 col-lg-3">
                <div class="border rounded-3 p-3 h-100">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="badge bg-{{ $meta['color'] }} d-inline-flex align-items-center gap-1">
                            <i class="bi {{ $meta['icon'] }}"></i>
                            {{ $meta['label'] }}
                        </span>
                    </div>
                    <p class="text-muted small mb-0" style="font-size:.78rem;line-height:1.4">
                        {{ $meta['desc'] }}
                    </p>
                </div>
            </div>
        @endforeach
    </div>
</div>

<div class="row g-4 align-items-start">

    {{-- ── Left column: Role cards ──────────────────────── --}}
    <div class="col-lg-8">
        <div class="row g-4">
            @foreach($roles as $role)
            @php $usersInRole = \App\Models\User::where('role_id', $role->id)->count(); @endphp
            <div class="col-12">
                <div class="bg-white rounded-3 shadow-sm">

                    {{-- Role header --}}
                    <div class="p-4 border-bottom d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="fw-bold mb-0">
                                <i class="bi bi-person-badge text-primary me-2"></i>{{ $role->name }}
                            </h5>
                            <span class="text-muted small">
                                {{ $role->permissions->count() }} {{ Str::plural('permission', $role->permissions->count()) }}
                                &middot;
                                <i class="bi bi-people me-1"></i>{{ $usersInRole }} {{ Str::plural('user', $usersInRole) }}
                            </span>
                        </div>

                        {{-- Delete role button --}}
                        @if($usersInRole === 0)
                            <form action="{{ route('admin.roles.destroy', $role) }}" method="POST"
                                  onsubmit="return confirm('Delete role \'{{ $role->name }}\'? This cannot be undone.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm">
                                    <i class="bi bi-trash me-1"></i>Delete Role
                                </button>
                            </form>
                        @else
                            <span class="text-muted small" title="Cannot delete — users are assigned to this role">
                                <i class="bi bi-lock me-1"></i>{{ $usersInRole }} {{ Str::plural('user', $usersInRole) }} assigned
                            </span>
                        @endif
                    </div>

                    {{-- Permissions form --}}
                    <form action="{{ route('admin.roles.updatePermissions', $role) }}" method="POST" class="p-4">
                        @csrf
                        @method('PATCH')

                        <div class="row g-2 mb-4">
                            @foreach($permissions as $permission)
                                @php
                                    $checked = $role->permissions->contains('id', $permission->id);
                                    $meta    = $permissionMeta[$permission->name] ?? ['label' => $permission->name, 'icon' => 'bi-key', 'color' => 'secondary', 'desc' => ''];
                                @endphp
                                <div class="col-md-6">
                                    <label class="d-flex gap-3 p-3 rounded-3 border h-100
                                                  {{ $checked ? 'border-primary bg-light' : 'border' }}"
                                           style="cursor:pointer">
                                        <input type="checkbox"
                                               class="form-check-input mt-1 flex-shrink-0"
                                               name="permissions[]"
                                               value="{{ $permission->id }}"
                                               {{ $checked ? 'checked' : '' }}>
                                        <div>
                                            <div class="d-flex align-items-center gap-1 mb-1">
                                                <i class="bi {{ $meta['icon'] }} text-{{ $meta['color'] }}"></i>
                                                <span class="small fw-600">{{ $meta['label'] }}</span>
                                            </div>
                                            <p class="text-muted mb-0" style="font-size:.75rem;line-height:1.4">
                                                {{ $meta['desc'] }}
                                            </p>
                                        </div>
                                    </label>
                                </div>
                            @endforeach
                        </div>

                        <button type="submit" class="btn btn-primary btn-sm px-4">
                            <i class="bi bi-check-circle me-1"></i>Save Permissions for {{ $role->name }}
                        </button>
                    </form>

                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- ── Right column: Create new role ───────────────── --}}
    <div class="col-lg-4">
        <div class="bg-white rounded-3 shadow-sm p-4 sticky-top" style="top:80px">
            <h6 class="fw-bold mb-1">
                <i class="bi bi-plus-circle text-success me-2"></i>Create New Role
            </h6>
            <p class="text-muted small mb-3">
                New roles start with no permissions. Assign them after creating.
            </p>

            <form action="{{ route('admin.roles.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="role_name" class="form-label">Role Name <span class="text-danger">*</span></label>
                    <input type="text"
                           class="form-control @error('name') is-invalid @enderror"
                           id="role_name"
                           name="name"
                           placeholder="e.g. Manager, Viewer…"
                           value="{{ old('name') }}"
                           maxlength="50">
                    @error('name')
                        <div class="invalid-feedback">
                            <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                        </div>
                    @enderror
                    <div class="form-text">Letters only, max 50 characters.</div>
                </div>

                <button type="submit" class="btn btn-success w-100">
                    <i class="bi bi-plus-lg me-1"></i>Create Role
                </button>
            </form>

            {{-- Existing roles quick list --}}
            <hr class="my-4">
            <h6 class="fw-bold mb-3 text-muted text-uppercase" style="font-size:.72rem;letter-spacing:.07em">
                Current Roles
            </h6>
            <div class="d-flex flex-column gap-2">
                @foreach($roles as $role)
                @php $count = \App\Models\User::where('role_id', $role->id)->count(); @endphp
                <div class="d-flex justify-content-between align-items-center p-2 bg-light rounded-2">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-person-badge text-primary"></i>
                        <span class="small fw-500">{{ $role->name }}</span>
                    </div>
                    <span class="badge bg-white border text-muted">
                        {{ $count }} {{ Str::plural('user', $count) }}
                    </span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

</div>

@endsection
