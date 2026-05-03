<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Case System') }}</title>

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    @endif
</head>
<body>
    <div class="min-vh-100 d-flex flex-column justify-content-center align-items-center bg-light py-5">
        <div class="mb-4 text-center">
            <a href="/" class="text-decoration-none">
                <h1 class="h3 mb-0">{{ config('app.name', 'Case System') }}</h1>
            </a>
            <p class="text-muted small mt-2">Please sign in to continue</p>
        </div>

        <div class="w-100" style="max-width: 480px;">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    {{ $slot }}
                </div>
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
