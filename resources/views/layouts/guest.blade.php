<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'ALMuhalab International Co.') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Cairo:wght@400;700;900&display=swap" rel="stylesheet">

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    @endif

    <style>
        body {
            font-family: 'Inter', system-ui, sans-serif;
            background: #0f172a;
            min-height: 100vh;
            margin: 0;
        }
        .login-bg {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
            background:
                radial-gradient(ellipse 80% 60% at 50% -10%, rgba(37,99,235,.18) 0%, transparent 70%),
                radial-gradient(ellipse 60% 40% at 80% 110%, rgba(245,158,11,.10) 0%, transparent 60%),
                #0f172a;
        }
        .login-card {
            width: 100%;
            max-width: 440px;
        }
        .login-brand {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: .75rem;
            margin-bottom: 2rem;
        }
        .login-brand-mark {
            width: 56px; height: 56px;
            background: linear-gradient(145deg, #b45309, #f59e0b 60%, #fbbf24);
            border-radius: 16px;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 4px 20px rgba(245,158,11,.30), inset 0 1px 0 rgba(255,255,255,.18);
            position: relative;
            overflow: hidden;
        }
        .login-brand-mark::before {
            content: '';
            position: absolute; inset: 0;
            background: linear-gradient(180deg, rgba(255,255,255,.12) 0%, transparent 55%);
        }
        .login-brand-letter {
            font-size: 1.8rem;
            font-weight: 900;
            color: #fff;
            font-family: 'Cairo', system-ui, sans-serif;
            line-height: 1;
            position: relative;
            text-shadow: 0 1px 4px rgba(0,0,0,.25);
        }
        .login-brand-text {
            text-align: center;
        }
        .login-brand-name {
            font-size: 1.25rem;
            font-weight: 700;
            color: #fff;
            letter-spacing: -.3px;
            display: block;
        }
        .login-brand-sub {
            font-size: .72rem;
            font-weight: 500;
            color: rgba(255,255,255,.4);
            letter-spacing: .1em;
            text-transform: uppercase;
            display: block;
            margin-top: .1rem;
        }
        .login-form-card {
            background: rgba(255,255,255,.04);
            border: 1px solid rgba(255,255,255,.08);
            border-radius: 16px;
            padding: 2rem;
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            box-shadow: 0 8px 40px rgba(0,0,0,.35);
        }
        .login-form-card .form-label {
            color: rgba(255,255,255,.65);
            font-size: .83rem;
            font-weight: 500;
        }
        .login-form-card .form-control {
            background: rgba(255,255,255,.06);
            border: 1px solid rgba(255,255,255,.10);
            color: #fff;
            border-radius: 8px;
            padding: .55rem .85rem;
            font-size: .9rem;
            transition: border-color .15s, background .15s;
        }
        .login-form-card .form-control:focus {
            background: rgba(255,255,255,.09);
            border-color: rgba(245,158,11,.5);
            box-shadow: 0 0 0 3px rgba(245,158,11,.12);
            color: #fff;
            outline: none;
        }
        .login-form-card .form-control::placeholder { color: rgba(255,255,255,.25); }
        .login-form-card .form-check-input {
            background-color: rgba(255,255,255,.08);
            border-color: rgba(255,255,255,.20);
        }
        .login-form-card .form-check-input:checked {
            background-color: #f59e0b;
            border-color: #f59e0b;
        }
        .login-form-card .form-check-label { color: rgba(255,255,255,.6); font-size: .83rem; }
        .btn-login {
            background: linear-gradient(135deg, #d97706, #f59e0b);
            border: none;
            color: #fff;
            font-weight: 600;
            font-size: .92rem;
            padding: .6rem 1.5rem;
            border-radius: 8px;
            width: 100%;
            transition: opacity .15s, transform .1s;
            box-shadow: 0 3px 12px rgba(245,158,11,.30);
        }
        .btn-login:hover { opacity: .9; transform: translateY(-1px); color: #fff; }
        .btn-login:active { transform: translateY(0); }
        .login-form-card a { color: #f59e0b; }
        .login-form-card a:hover { color: #fbbf24; }
        .login-divider {
            border-color: rgba(255,255,255,.08);
            margin: 1.25rem 0;
        }
        .invalid-feedback { font-size: .78rem; }
        .login-form-card .is-invalid {
            border-color: rgba(248,113,113,.5) !important;
        }
        .login-footer {
            text-align: center;
            margin-top: 1.5rem;
            font-size: .72rem;
            color: rgba(255,255,255,.22);
        }
    </style>
</head>
<body>
    <div class="login-bg">
        <div class="login-card">

            {{-- Brand --}}
            <div class="login-brand">
                <div class="login-brand-mark">
                    <span class="login-brand-letter">م</span>
                </div>
                <div class="login-brand-text">
                    <span class="login-brand-name">ALMuhalab</span>
                    <span class="login-brand-sub">International Co.</span>
                </div>
            </div>

            {{-- Form Card --}}
            <div class="login-form-card">
                {{ $slot }}
            </div>

            <div class="login-footer">
                &copy; {{ date('Y') }} ALMuhalab International Co. — Kuwait City
            </div>

        </div>
    </div>

@if (!(file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot'))))
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@else
    @vite(['resources/js/app.js'])
@endif
</body>
</html>
