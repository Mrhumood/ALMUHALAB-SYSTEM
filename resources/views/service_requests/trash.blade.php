@extends('layouts.app')

@section('title','Trashed Service Requests')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h2 mb-0">Trash</h1>
            <p class="text-muted small mt-1">Recently deleted service requests</p>
        </div>
        <div class="btn-group">
            <a href="{{ route('service-requests.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to list
            </a>
        </div>
    </div>

    @if($items->isEmpty())
        <div class="alert alert-info text-center" role="alert">
            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
            <strong>No trashed requests.</strong>
            <p class="mb-0 mt-2">Deleted requests will appear here and can be restored or permanently removed.</p>
        </div>
    @else
        <div class="table-responsive shadow-sm rounded">
            <table class="table mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width:5%">#</th>
                        <th style="width:50%">Title</th>
                        <th style="width:20%">Deleted</th>
                        <th style="width:25%">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $item)
                        <tr>
                            <td class="fw-bold text-muted">{{ $item->id }}</td>
                            <td>
                                <a href="{{ route('service-requests.show', $item) }}" class="text-decoration-none">
                                    {{ Str::limit($item->title, 80) }}
                                </a>
                            </td>
                            <td class="text-muted small">{{ $item->deleted_at->format('M d, Y H:i') }}</td>
                            <td>
                                <div class="btn-group">
                                    <form action="{{ route('service-requests.restore', $item->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-success">
                                            <i class="bi bi-arrow-counterclockwise me-1"></i>Restore
                                        </button>
                                    </form>

                                    <form action="{{ route('service-requests.forceDelete', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Permanently delete this request? This cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash me-1"></i>Delete Permanently
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
