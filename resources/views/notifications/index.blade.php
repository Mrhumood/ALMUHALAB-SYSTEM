@extends('layouts.app')
@section('title', __('Notifications'))

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h4 fw-bold mb-0"><i class="bi bi-bell text-primary me-2"></i>{{ __('Notifications') }}</h1>
        <p class="text-muted small mb-0">
            {{ $unread }} {{ __('unread') }} · {{ $notifications->total() }} {{ __('total') }}
        </p>
    </div>
    @if($unread > 0)
    <form action="{{ route('notifications.read-all') }}" method="POST">
        @csrf @method('PATCH')
        <button class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-check2-all me-1"></i>{{ __('Mark all as read') }}
        </button>
    </form>
    @endif
</div>

<div class="page-card p-0 overflow-hidden">

    @forelse($notifications as $notif)
        @php $d = $notif->data; @endphp
        <div class="d-flex align-items-start gap-3 p-3 border-bottom {{ $notif->read_at ? '' : 'bg-primary bg-opacity-10' }}"
             style="transition:background .15s">

            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0
                        bg-{{ $d['color'] ?? 'primary' }} bg-opacity-15 text-{{ $d['color'] ?? 'primary' }}"
                 style="width:2.4rem;height:2.4rem;font-size:1rem">
                <i class="bi {{ $d['icon'] ?? 'bi-bell' }}"></i>
            </div>

            <div class="flex-grow-1 overflow-hidden">
                <div class="d-flex justify-content-between align-items-start gap-2">
                    <div>
                        <div class="fw-600 small">{{ $d['title'] ?? __('Notification') }}</div>
                        <div class="text-muted small mt-1">{{ $d['message'] ?? '' }}</div>
                    </div>
                    @if(!$notif->read_at)
                        <span class="badge bg-primary rounded-pill flex-shrink-0" style="font-size:.6rem">{{ __('New') }}</span>
                    @endif
                </div>
                <div class="d-flex align-items-center gap-3 mt-2" style="font-size:.75rem">
                    <span class="text-muted">
                        <i class="bi bi-clock me-1"></i>{{ $notif->created_at->diffForHumans() }}
                    </span>
                    @if(!empty($d['url']))
                        <a href="{{ $d['url'] }}" class="text-primary text-decoration-none fw-500">
                            {{ __('View request') }} <i class="bi bi-arrow-right"></i>
                        </a>
                    @endif
                    @if(!$notif->read_at)
                        <form action="{{ route('notifications.read', $notif->id) }}" method="POST" class="d-inline">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn btn-link btn-sm p-0 text-muted text-decoration-none" style="font-size:.75rem">
                                {{ __('Mark as read') }}
                            </button>
                        </form>
                    @endif
                </div>
            </div>

        </div>
    @empty
        <div class="text-center py-5 text-muted">
            <i class="bi bi-bell-slash d-block mb-2" style="font-size:2.5rem;opacity:.3"></i>
            <div class="small">{{ __('No notifications yet.') }}</div>
        </div>
    @endforelse

</div>

@if($notifications->hasPages())
    <div class="d-flex justify-content-between align-items-center mt-3 px-1">
        <span class="text-muted" style="font-size:.8rem">
            {{ __('Showing :first–:last of :total', ['first' => $notifications->firstItem(), 'last' => $notifications->lastItem(), 'total' => $notifications->total()]) }}
        </span>
        {{ $notifications->links('pagination::bootstrap-5') }}
    </div>
@endif

@endsection
