@extends('layouts.app')

@section('title', __('Trash'))

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h4 fw-bold mb-0"><i class="bi bi-trash text-danger me-2"></i>{{ __('Trash') }}</h1>
        <p class="text-muted small mb-0">{{ __('Deleted requests — restore or remove permanently') }}</p>
    </div>
    <a href="{{ route('service-requests.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>{{ __('Back to Requests') }}
    </a>
</div>

@if($items->isEmpty())
    <div class="page-card text-center py-5">
        <i class="bi bi-trash fs-1 text-muted d-block mb-3"></i>
        <h5 class="text-muted">{{ __('Trash is empty') }}</h5>
        <p class="text-muted small">{{ __('Deleted requests will appear here.') }}</p>
    </div>
@else
    <div class="bg-white rounded-3 shadow-sm overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr class="bg-light border-bottom">
                        <th class="ps-4 text-muted fw-600 small" style="width:5%">#</th>
                        <th class="text-muted fw-600 small" style="width:42%">{{ __('TITLE') }}</th>
                        <th class="text-muted fw-600 small" style="width:18%">{{ __('STATUS') }}</th>
                        <th class="text-muted fw-600 small" style="width:15%">{{ __('DELETED') }}</th>
                        <th class="text-muted fw-600 small pe-4" style="width:20%">{{ __('ACTIONS') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $item)
                        @php
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
                            <td class="ps-4 text-muted small">{{ $loop->iteration }}</td>
                            <td>
                                <a href="{{ route('service-requests.showTrashed', $item->id) }}"
                                   class="text-decoration-none text-dark fw-500">
                                    {{ Str::limit($item->title, 55) }}
                                </a>
                            </td>
                            <td>
                                <span class="badge badge-status {{ $badgeClass }}">{{ $badgeLabel }}</span>
                            </td>
                            <td class="text-muted small">
                                <i class="bi bi-clock me-1"></i>{{ $item->deleted_at->format('d M Y') }}
                            </td>
                            <td class="pe-4">
                                <div class="d-flex gap-1 flex-wrap">
                                    <a href="{{ route('service-requests.showTrashed', $item->id) }}"
                                       class="btn btn-outline-secondary btn-action btn-sm">
                                        <i class="bi bi-eye"></i>
                                    </a>

                                    <form action="{{ route('service-requests.restore', $item->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-success btn-action btn-sm"
                                                title="{{ __('Restore') }}">
                                            <i class="bi bi-arrow-counterclockwise"></i> {{ __('Restore') }}
                                        </button>
                                    </form>

                                    <form action="{{ route('service-requests.forceDelete', $item->id) }}" method="POST"
                                          onsubmit="return confirm('{{ __('Permanently delete? This cannot be undone.') }}')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-action btn-sm"
                                                title="{{ __('Delete Permanently') }}">
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
        <div class="px-4 py-2 border-top bg-light small text-muted">
            {{ $items->count() }} {{ __('item(s) in trash') }}
        </div>
    </div>
@endif

@endsection
