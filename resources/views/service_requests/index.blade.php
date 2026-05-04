@extends('layouts.app')

@section('title','Service Requests')

@section('content')

{{-- Stats Bar --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3 col-xl">
        <div class="stat-card card text-center py-3 px-2 bg-white">
            <div class="fs-2 fw-bold text-dark">{{ $stats['total'] }}</div>
            <div class="small text-muted mt-1">Total</div>
        </div>
    </div>
    <div class="col-6 col-md-3 col-xl">
        <div class="stat-card card text-center py-3 px-2 bg-white">
            <div class="fs-2 fw-bold text-primary">{{ $stats['new'] }}</div>
            <div class="small text-muted mt-1">New</div>
        </div>
    </div>
    <div class="col-6 col-md-3 col-xl">
        <div class="stat-card card text-center py-3 px-2 bg-white">
            <div class="fs-2 fw-bold text-info">{{ $stats['under_review'] }}</div>
            <div class="small text-muted mt-1">Under Review</div>
        </div>
    </div>
    <div class="col-6 col-md-3 col-xl">
        <div class="stat-card card text-center py-3 px-2 bg-white">
            <div class="fs-2 fw-bold text-success">{{ $stats['approved'] }}</div>
            <div class="small text-muted mt-1">Approved</div>
        </div>
    </div>
    <div class="col-6 col-md-3 col-xl">
        <div class="stat-card card text-center py-3 px-2 bg-white">
            <div class="fs-2 fw-bold text-secondary">{{ $stats['completed'] }}</div>
            <div class="small text-muted mt-1">Completed</div>
        </div>
    </div>
</div>

{{-- Header --}}
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h1 class="h4 fw-bold mb-0">Service Requests</h1>
        <p class="text-muted small mb-0">All active requests</p>
    </div>
</div>

@if($items->isEmpty())
    <div class="page-card text-center py-5">
        <i class="bi bi-inbox fs-1 text-muted d-block mb-3"></i>
        <h5 class="text-muted">No service requests yet</h5>
        <p class="text-muted small">Create your first request to get started.</p>
        <a href="{{ route('service-requests.create') }}" class="btn btn-primary mt-2">
            <i class="bi bi-plus-lg me-1"></i>New Request
        </a>
    </div>
@else
    <div class="bg-white rounded-3 shadow-sm overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr class="bg-light border-bottom">
                        <th class="ps-4 text-muted fw-600 small" style="width:5%">#</th>
                        <th class="text-muted fw-600 small" style="width:30%">TITLE</th>
                        <th class="text-muted fw-600 small" style="width:15%">STATUS</th>
                        @if($isAdmin)
                        <th class="text-muted fw-600 small" style="width:13%">SUBMITTED BY</th>
                        @endif
                        <th class="text-muted fw-600 small" style="width:12%">DATE</th>
                        <th class="text-muted fw-600 small pe-4" style="width:25%">ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $item)
                        @php
                            $rowNum = ($items->currentPage() - 1) * $items->perPage() + $loop->iteration;
                            $statusConfig = [
                                'New'          => ['bg-primary',   'New'],
                                'Under Review' => ['bg-info',      'Under Review'],
                                'Approved'     => ['bg-success',   'Approved'],
                                'Rejected'     => ['bg-danger',    'Rejected'],
                                'Completed'    => ['bg-secondary', 'Completed'],
                            ];
                            [$badgeClass, $badgeLabel] = $statusConfig[$item->status] ?? ['bg-light text-dark', $item->status];
                        @endphp
                        <tr class="align-middle border-bottom">
                            <td class="ps-4 text-muted small">{{ $rowNum }}</td>
                            <td>
                                <a href="{{ route('service-requests.show', $item) }}"
                                   class="text-decoration-none text-dark fw-500 stretched-link-title">
                                    {{ Str::limit($item->title, 55) }}
                                </a>
                                @if($item->attachment_path)
                                    <i class="bi bi-paperclip text-muted ms-1" title="Has attachment"></i>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-status {{ $badgeClass }}">{{ $badgeLabel }}</span>
                            </td>
                            @if($isAdmin)
                            <td class="text-muted small">
                                <i class="bi bi-person me-1"></i>{{ $item->user->name }}
                            </td>
                            @endif
                            <td class="text-muted small">
                                <i class="bi bi-calendar3 me-1"></i>{{ $item->created_at->format('d M Y') }}
                            </td>
                            <td class="pe-4">
                                <div class="d-flex gap-1 flex-wrap">
                                    <a href="{{ route('service-requests.show', $item) }}"
                                       class="btn btn-outline-secondary btn-action btn-sm">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                    <a href="{{ route('service-requests.edit', $item) }}"
                                       class="btn btn-outline-primary btn-action btn-sm">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    <form action="{{ route('service-requests.destroy', $item) }}" method="POST"
                                          onsubmit="return confirm('Move this request to trash?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-action btn-sm">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($items->hasPages())
            <div class="d-flex justify-content-between align-items-center px-4 py-3 border-top bg-light">
                <div class="small text-muted">
                    Showing {{ $items->firstItem() }}–{{ $items->lastItem() }} of {{ $items->total() }} requests
                </div>
                {{ $items->links('pagination::bootstrap-5') }}
            </div>
        @else
            <div class="px-4 py-2 border-top bg-light small text-muted">
                {{ $items->total() }} {{ Str::plural('request', $items->total()) }}
            </div>
        @endif
    </div>
@endif

@endsection
