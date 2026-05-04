<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'Case System')) — {{ config('app.name', 'Case System') }}</title>

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    @endif

    <style>
        :root {
            --bs-body-bg: #f4f6f9;
        }
        body { background-color: var(--bs-body-bg); }

        .navbar-brand-text { font-weight: 700; letter-spacing: -.3px; }

        .nav-link.active, .nav-link:hover { background: rgba(255,255,255,.12); border-radius: 6px; }

        .flash-msg { border-left: 4px solid; border-radius: 6px; }
        .flash-msg.success { border-color: #198754; }
        .flash-msg.danger  { border-color: #dc3545; }

        .stat-card { border: none; border-radius: 12px; transition: transform .15s; }
        .stat-card:hover { transform: translateY(-2px); }

        .table > :not(caption) > * > * { padding: .85rem 1rem; }
        .table-hover tbody tr:hover { background-color: #eef2ff; cursor: default; }

        .badge-status { font-size: .78rem; padding: .35em .7em; border-radius: 20px; font-weight: 600; }

        .btn-action { padding: .3rem .7rem; font-size: .82rem; }

        .page-card { background: #fff; border-radius: 14px; box-shadow: 0 1px 6px rgba(0,0,0,.07); padding: 2rem; }

        .form-label { font-weight: 500; font-size: .9rem; }
        .form-control, .form-select { border-radius: 8px; }
        .form-control:focus, .form-select:focus { box-shadow: 0 0 0 3px rgba(13,110,253,.15); }

        /* ── Timeline (shared) ── */
        .tl-wrapper { position: relative; }
        .tl-item { display: flex; gap: 1rem; }
        .tl-left { display: flex; flex-direction: column; align-items: center; flex-shrink: 0; width: 2.25rem; }
        .tl-marker {
            width: 2.25rem; height: 2.25rem; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: .9rem; flex-shrink: 0; z-index: 1;
        }
        .tl-marker-done    { background: #198754; color: #fff; }
        .tl-marker-current { background: #0d6efd; color: #fff; animation: tl-pulse 2s infinite; }
        .tl-marker-future  { background: #fff; border: 2px solid #dee2e6; color: #adb5bd; }
        .tl-marker-audit   { color: #fff; }
        .tl-connector { flex: 1; width: 2px; background: #e9ecef; min-height: 1.5rem; margin: .25rem 0; }
        .tl-connector-done { background: #198754; }
        .tl-body { flex: 1; padding-bottom: 1.5rem; }
        .tl-card {
            background: #fff; border: 1px solid #dee2e6; border-radius: .625rem;
            padding: 1rem; transition: box-shadow .15s;
        }
        .tl-card:hover { box-shadow: 0 2px 8px rgba(0,0,0,.07); }
        .tl-card-current { border-color: #0d6efd; border-width: 2px; background: #f8fbff; }
        .tl-card-done { background: #f8f9fa; }
        .tl-item.tl-future .tl-card { opacity: .85; }
        .tl-item:last-child .tl-body { padding-bottom: 0; }
        @keyframes tl-pulse {
            0%,100% { box-shadow: 0 0 0 0 rgba(13,110,253,.35); }
            50%      { box-shadow: 0 0 0 8px rgba(13,110,253,.0); }
        }
        .fw-500 { font-weight: 500; }
        .fw-600 { font-weight: 600; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
  <div class="container-fluid px-4">
    <a class="navbar-brand navbar-brand-text d-flex align-items-center gap-2" href="{{ route('service-requests.index') }}">
        <i class="bi bi-layers-fill text-primary"></i>
        {{ config('app.name', 'Case System') }}
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navMain">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0 gap-1">
        <li class="nav-item">
            <a class="nav-link px-3 {{ request()->routeIs('service-requests.index') ? 'active' : '' }}"
               href="{{ route('service-requests.index') }}">
                <i class="bi bi-list-ul me-1"></i>Requests
            </a>
        </li>
        @auth
            @if(auth()->user()->hasPermission('view_trash'))
            <li class="nav-item">
                <a class="nav-link px-3 {{ request()->routeIs('service-requests.trash') ? 'active' : '' }}"
                   href="{{ route('service-requests.trash') }}">
                    <i class="bi bi-trash me-1"></i>Trash
                </a>
            </li>
            @endif
            @if(auth()->user()->hasPermission('manage_users'))
            <li class="nav-item dropdown">
                <a class="nav-link px-3 dropdown-toggle {{ request()->routeIs('admin.*') ? 'active' : '' }}"
                   href="#" data-bs-toggle="dropdown">
                    <i class="bi bi-shield-lock me-1"></i>Admin
                </a>
                <ul class="dropdown-menu dropdown-menu-dark border-0 shadow mt-1">
                    <li>
                        <a class="dropdown-item" href="{{ route('admin.users.index') }}">
                            <i class="bi bi-people me-2"></i>Users
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('admin.roles.index') }}">
                            <i class="bi bi-shield-lock me-2"></i>Roles & Permissions
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('admin.service-types.index') }}">
                            <i class="bi bi-tags me-2"></i>Service Types
                        </a>
                    </li>
                    @if(auth()->user()->hasPermission('view_audit_log'))
                    <li><hr class="dropdown-divider border-secondary my-1"></li>
                    <li>
                        <a class="dropdown-item" href="{{ route('admin.audit-log.index') }}">
                            <i class="bi bi-clock-history me-2"></i>Audit Log
                        </a>
                    </li>
                    @endif
                </ul>
            </li>
            @endif
        @endauth
      </ul>

      <ul class="navbar-nav ms-auto align-items-center gap-2">
        @guest
            <li class="nav-item"><a class="btn btn-outline-light btn-sm" href="{{ route('login') }}">Login</a></li>
            <li class="nav-item"><a class="btn btn-primary btn-sm" href="{{ route('register') }}">Register</a></li>
        @else
            <li class="nav-item">
                <a class="btn btn-success btn-sm" href="{{ route('service-requests.create') }}">
                    <i class="bi bi-plus-lg me-1"></i>New Request
                </a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle d-flex align-items-center gap-2 text-white" href="#"
                   id="userMenu" role="button" data-bs-toggle="dropdown">
                    <span class="d-inline-flex align-items-center justify-content-center bg-primary rounded-circle text-white"
                          style="width:30px;height:30px;font-size:.8rem;font-weight:700;">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </span>
                    <span class="d-none d-lg-inline">{{ Auth::user()->name }}</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-1" aria-labelledby="userMenu">
                    <li class="px-3 py-2 text-muted small border-bottom">
                        {{ Auth::user()->email }}
                        @if(Auth::user()->role)
                            <span class="badge bg-secondary ms-1">{{ Auth::user()->role->name }}</span>
                        @endif
                    </li>
                    <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="bi bi-person me-2"></i>Profile</a></li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button class="dropdown-item text-danger" type="submit">
                                <i class="bi bi-box-arrow-right me-2"></i>Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </li>
        @endguest
      </ul>
    </div>
  </div>
</nav>

<div class="container-fluid px-4 py-3" style="max-width: 1280px;">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible flash-msg success d-flex align-items-center gap-2 mb-3" role="alert">
            <i class="bi bi-check-circle-fill fs-5"></i>
            <span>{{ session('success') }}</span>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible flash-msg danger d-flex align-items-center gap-2 mb-3" role="alert">
            <i class="bi bi-exclamation-circle-fill fs-5"></i>
            <span>{{ session('error') }}</span>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @yield('content')
</div>

@if (!(file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot'))))
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
@else
    @vite(['resources/js/app.js'])
@endif

<script>
    // Auto-dismiss flash messages after 4 seconds
    document.querySelectorAll('.flash-msg').forEach(el => {
        setTimeout(() => {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(el);
            bsAlert.close();
        }, 4000);
    });
</script>
</body>
</html>
