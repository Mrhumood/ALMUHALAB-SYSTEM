@extends('layouts.app')

@section('title','Trashed — '.$serviceRequest->title)

@section('content')
<div class="row justify-content-center">
<div class="col-lg-8">

    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ route('service-requests.trash') }}">Trash</a></li>
            <li class="breadcrumb-item active">{{ Str::limit($serviceRequest->title, 40) }}</li>
        </ol>
    </nav>

    {{-- Trashed notice --}}
    <div class="alert alert-warning d-flex align-items-center gap-2 mb-3">
        <i class="bi bi-exclamation-triangle-fill"></i>
        <span>This request is in trash. Restore it to make it active again, or delete it permanently.</span>
    </div>

    <div class="page-card">
        <div class="d-flex justify-content-between align-items-start mb-4">
            <div>
                <h2 class="h4 fw-bold mb-1">{{ $serviceRequest->title }}</h2>
                <span class="text-muted small">
                    <i class="bi bi-trash me-1"></i>Deleted {{ $serviceRequest->deleted_at->format('d M Y \a\t H:i') }}
                </span>
            </div>
            @php
                $statusConfig = [
                    'New'          => ['bg-primary',   'New'],
                    'Under Review' => ['bg-info',      'Under Review'],
                    'Approved'     => ['bg-success',   'Approved'],
                    'Rejected'     => ['bg-danger',    'Rejected'],
                    'Completed'    => ['bg-secondary', 'Completed'],
                ];
                [$badgeClass, $badgeLabel] = $statusConfig[$serviceRequest->status] ?? ['bg-light text-dark', $serviceRequest->status];
            @endphp
            <span class="badge badge-status {{ $badgeClass }}">{{ $badgeLabel }}</span>
        </div>

        <hr class="my-3">

        <div class="mb-4">
            <p class="text-muted small fw-600 mb-1 text-uppercase" style="letter-spacing:.05em">Description</p>
            <div class="bg-light rounded-3 p-3" style="white-space: pre-wrap; line-height: 1.7;">{{ $serviceRequest->description }}</div>
        </div>

        <div class="mb-4">
            <p class="text-muted small fw-600 mb-1 text-uppercase" style="letter-spacing:.05em">Attachment</p>
            @if($serviceRequest->attachment_path)
                <a href="{{ asset('storage/'.$serviceRequest->attachment_path) }}"
                   class="btn btn-outline-primary btn-sm" target="_blank">
                    <i class="bi bi-download me-1"></i>Download File
                </a>
            @else
                <span class="text-muted small"><i class="bi bi-dash me-1"></i>No attachment</span>
            @endif
        </div>

        <hr class="my-3">

        <div class="d-flex gap-2 flex-wrap">
            <form action="{{ route('service-requests.restore', $serviceRequest->id) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-arrow-counterclockwise me-1"></i>Restore
                </button>
            </form>

            <form action="{{ route('service-requests.forceDelete', $serviceRequest->id) }}" method="POST"
                  onsubmit="return confirm('Permanently delete this request? This cannot be undone.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="bi bi-trash me-1"></i>Delete Permanently
                </button>
            </form>

            <a href="{{ route('service-requests.trash') }}" class="btn btn-outline-secondary ms-auto">
                <i class="bi bi-arrow-left me-1"></i>Back to Trash
            </a>
        </div>
    </div>

</div>
</div>
@endsection
