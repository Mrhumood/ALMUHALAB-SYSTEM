@extends('layouts.app')

@section('title','Trashed Service Request Details')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-warning mb-4">
                <div class="card-header bg-warning text-dark border-0 py-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <h2 class="mb-0">
                            <i class="bi bi-trash me-2"></i>{{ $serviceRequest->title }}
                        </h2>
                        <span class="badge bg-secondary">Trashed</span>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="alert alert-warning alert-dismissible fade show mb-4" role="alert">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>This request is in trash.</strong> Restore it to bring it back or delete permanently.
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <p class="text-muted small mb-1">Status</p>
                            @php
                                $statusMap = [
                                    'New' => ['badge bg-primary', 'New'],
                                    'Under Review' => ['badge bg-info text-dark', 'Under Review'],
                                    'Approved' => ['badge bg-success', 'Approved'],
                                    'Rejected' => ['badge bg-danger', 'Rejected'],
                                    'Completed' => ['badge bg-secondary', 'Completed'],
                                    'open' => ['badge bg-primary', 'New'],
                                ];
                                $current = $statusMap[$serviceRequest->status] ?? ['badge bg-light text-dark', $serviceRequest->status];
                            @endphp
                            <span class="{{ $current[0] }} fs-5">{{ $current[1] }}</span>
                        </div>
                        <div class="col-md-6">
                            <p class="text-muted small mb-1">Deleted</p>
                            <p class="mb-0">
                                <i class="bi bi-trash me-1"></i>{{ $serviceRequest->deleted_at->format('M d, Y \a\t H:i') }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p class="text-muted small mb-1">Created</p>
                            <p class="mb-0">
                                <i class="bi bi-calendar3 me-1"></i>{{ $serviceRequest->created_at->format('M d, Y \a\t H:i') }}
                            </p>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="mb-4">
                        <h5 class="mb-3">
                            <i class="bi bi-file-earmark-text me-2"></i>Description
                        </h5>
                        <p class="bg-light p-3 rounded">{{ $serviceRequest->description }}</p>
                    </div>

                    <div class="mb-4">
                        <h5 class="mb-3">
                            <i class="bi bi-paperclip me-2"></i>Attachment
                        </h5>
                        @if($serviceRequest->attachment_path)
                            <a href="{{ asset('storage/'.$serviceRequest->attachment_path) }}" 
                               class="btn btn-outline-primary" target="_blank">
                                <i class="bi bi-download me-2"></i>Download Attachment
                            </a>
                        @else
                            <div class="alert alert-secondary mb-0" role="alert">
                                <i class="bi bi-inbox me-2"></i>No attachment
                            </div>
                        @endif
                    </div>

                    <hr class="my-4">

                    <div class="d-flex gap-2 flex-wrap">
                        <form action="{{ route('service-requests.restore', $serviceRequest->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="bi bi-arrow-counterclockwise me-2"></i>Restore
                            </button>
                        </form>

                        <form action="{{ route('service-requests.forceDelete', $serviceRequest->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Permanently delete this request? This cannot be undone.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-lg">
                                <i class="bi bi-trash me-2"></i>Delete Permanently
                            </button>
                        </form>

                        <a href="{{ route('service-requests.trash') }}" class="btn btn-secondary btn-lg">
                            <i class="bi bi-arrow-left me-2"></i>Back to Trash
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
