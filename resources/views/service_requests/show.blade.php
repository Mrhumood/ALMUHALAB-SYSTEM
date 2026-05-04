@extends('layouts.app')
@section('title', $serviceRequest->title)

@php
    $canManage  = auth()->user()->hasPermission('manage_followups');
    $canEdit    = auth()->user()->hasPermission('edit_request') || $serviceRequest->user_id === auth()->id();
    $canDelete  = auth()->user()->hasPermission('delete_request') || $serviceRequest->user_id === auth()->id();
    $canAudit   = auth()->user()->hasPermission('view_audit_log');

    // Load follow-ups: non-managers see only client-visible ones
    $allFollowUps = $canManage
        ? $serviceRequest->followUps()->with('creator')->get()
        : $serviceRequest->followUps()->where('is_visible_to_client', true)->with('creator')->get();

    // Determine "current" = first incomplete follow-up (chronological)
    $currentFollowUp = $allFollowUps->where('is_completed', false)->first();

    $statusConfig = [
        'New'          => ['bg-primary',   'bi-inbox',          'New'],
        'Under Review' => ['bg-info',      'bi-eye',            'Under Review'],
        'Approved'     => ['bg-success',   'bi-check-circle',   'Approved'],
        'Rejected'     => ['bg-danger',    'bi-x-circle',       'Rejected'],
        'Completed'    => ['bg-secondary', 'bi-check-all',      'Completed'],
    ];
    [$badgeClass, $badgeIcon, $badgeLabel] = $statusConfig[$serviceRequest->status]
        ?? ['bg-light text-dark', 'bi-circle', $serviceRequest->status];
@endphp

@section('content')
<div class="row justify-content-center">
<div class="col-lg-10">

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ route('service-requests.index') }}">Requests</a></li>
            <li class="breadcrumb-item active">{{ Str::limit($serviceRequest->title, 40) }}</li>
        </ol>
    </nav>

    {{-- ── Header ──────────────────────────────────────────── --}}
    <div class="page-card mb-4">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
            <div class="flex-grow-1">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="badge {{ $badgeClass }} px-3 py-2 fs-6">
                        <i class="bi {{ $badgeIcon }} me-1"></i>{{ $badgeLabel }}
                    </span>
                    @if($serviceRequest->serviceType->name !== '—')
                        <span class="badge bg-light text-dark border">
                            <i class="bi bi-tag me-1"></i>{{ $serviceRequest->serviceType->name }}
                        </span>
                    @endif
                </div>
                <h2 class="h4 fw-bold mb-1 mt-2">{{ $serviceRequest->title }}</h2>
                <div class="text-muted small">
                    <i class="bi bi-calendar3 me-1"></i>Submitted {{ $serviceRequest->created_at->format('d M Y') }}
                    @if(auth()->user()->hasPermission('edit_request'))
                        &nbsp;·&nbsp;<i class="bi bi-person me-1"></i>{{ $serviceRequest->user->name }}
                    @endif
                    @if($canAudit)
                        &nbsp;·&nbsp;
                        <a href="{{ route('admin.audit-log.show', $serviceRequest) }}" class="text-muted">
                            <i class="bi bi-clock-history me-1"></i>Audit Log
                        </a>
                    @endif
                </div>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                @if($canEdit)
                    <a href="{{ route('service-requests.edit', $serviceRequest) }}" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-pencil me-1"></i>Edit Request
                    </a>
                @endif
                @if($canDelete)
                    <form action="{{ route('service-requests.destroy', $serviceRequest) }}" method="POST"
                          onsubmit="return confirm('Move to trash?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-sm">
                            <i class="bi bi-trash me-1"></i>Delete
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <div class="row g-4">

        {{-- ── LEFT COLUMN: Timeline ───────────────────────────── --}}
        <div class="col-lg-8">

            {{-- Timeline Card --}}
            <div class="page-card">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h6 class="fw-bold mb-0 text-uppercase text-muted" style="font-size:.72rem;letter-spacing:.07em">
                        <i class="bi bi-diagram-3 me-1"></i>Journey Timeline
                    </h6>
                    @if($canManage)
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addFollowUpModal">
                            <i class="bi bi-plus-lg me-1"></i>Add Step
                        </button>
                    @endif
                </div>

                @if($allFollowUps->isEmpty())
                    {{-- Empty state --}}
                    <div class="text-center py-5">
                        <div class="mb-3" style="font-size:2.5rem;opacity:.25;">
                            <i class="bi bi-diagram-3"></i>
                        </div>
                        @if($canManage)
                            <p class="text-muted mb-3">No steps added yet. Build the client's journey by adding the first step.</p>
                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addFollowUpModal">
                                <i class="bi bi-plus-lg me-1"></i>Add First Step
                            </button>
                        @else
                            <p class="text-muted mb-0">Your request has been received and is being reviewed. Updates will appear here.</p>
                        @endif
                    </div>
                @else
                    <div class="tl-wrapper">
                        @foreach($allFollowUps as $i => $fu)
                            @php
                                $cfg        = $fu->statusConfig();
                                $isCurrent  = $currentFollowUp && $fu->id === $currentFollowUp->id;
                                $isLast     = $i === $allFollowUps->count() - 1;
                                $stateClass = $fu->is_completed ? 'tl-done' : ($isCurrent ? 'tl-current' : 'tl-future');
                            @endphp

                            <div class="tl-item {{ $stateClass }}" id="fu-{{ $fu->id }}">
                                <div class="tl-left">
                                    <div class="tl-marker tl-marker-{{ $fu->is_completed ? 'done' : ($isCurrent ? 'current' : 'future') }}">
                                        @if($fu->is_completed)
                                            <i class="bi bi-check-lg"></i>
                                        @elseif($isCurrent)
                                            <i class="bi {{ $cfg['icon'] }}"></i>
                                        @else
                                            <i class="bi bi-clock"></i>
                                        @endif
                                    </div>
                                    @if(!$isLast)
                                        <div class="tl-connector {{ $fu->is_completed ? 'tl-connector-done' : '' }}"></div>
                                    @endif
                                </div>

                                <div class="tl-body">
                                    <div class="tl-card {{ $isCurrent ? 'tl-card-current' : '' }} {{ $fu->is_completed ? 'tl-card-done' : '' }}">

                                        {{-- Card header --}}
                                        <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-2">
                                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                                <span class="badge bg-{{ $cfg['color'] }}-subtle text-{{ $cfg['color'] }} border border-{{ $cfg['color'] }}-subtle" style="font-size:.7rem">
                                                    <i class="bi {{ $cfg['icon'] }} me-1"></i>{{ $cfg['label'] }}
                                                </span>
                                                @if(!$fu->is_visible_to_client && $canManage)
                                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle" style="font-size:.7rem">
                                                        <i class="bi bi-eye-slash me-1"></i>Private
                                                    </span>
                                                @endif
                                                @if($isCurrent)
                                                    <span class="badge bg-primary" style="font-size:.7rem">
                                                        <i class="bi bi-arrow-right-circle me-1"></i>Current Stage
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="text-muted" style="font-size:.72rem">
                                                @if($fu->scheduled_at)
                                                    <i class="bi bi-calendar-event me-1"></i>
                                                    {{ $fu->is_completed && $fu->completed_at
                                                        ? 'Completed ' . $fu->completed_at->format('d M Y')
                                                        : 'Scheduled ' . $fu->scheduled_at->format('d M Y') }}
                                                @elseif($fu->completed_at)
                                                    <i class="bi bi-check-circle me-1"></i>{{ $fu->completed_at->format('d M Y') }}
                                                @else
                                                    <i class="bi bi-clock me-1"></i>Added {{ $fu->created_at->format('d M Y') }}
                                                @endif
                                            </div>
                                        </div>

                                        {{-- Title --}}
                                        <div class="fw-600 mb-1" style="font-size:.95rem">{{ $fu->title }}</div>

                                        {{-- Description --}}
                                        @if($fu->description)
                                            <div class="text-muted small" style="line-height:1.6;white-space:pre-wrap">{{ $fu->description }}</div>
                                        @endif

                                        {{-- Extra Data --}}
                                        @if($fu->extra_data && count($fu->extra_data))
                                            <div class="mt-2 pt-2 border-top">
                                                <div class="row g-2">
                                                    @foreach($fu->extra_data as $key => $val)
                                                        @if($val)
                                                        <div class="col-sm-6">
                                                            <div class="text-muted" style="font-size:.7rem;text-transform:uppercase;letter-spacing:.05em">
                                                                {{ ucwords(str_replace('_', ' ', $key)) }}
                                                            </div>
                                                            <div class="small fw-500">{{ $val }}</div>
                                                        </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif

                                        {{-- Employee Controls --}}
                                        @if($canManage)
                                            <div class="mt-3 pt-2 border-top d-flex gap-2 flex-wrap align-items-center">
                                                {{-- Toggle complete --}}
                                                <form action="{{ route('follow-ups.toggle', [$serviceRequest, $fu]) }}" method="POST">
                                                    @csrf @method('PATCH')
                                                    <button type="submit"
                                                            class="btn btn-sm {{ $fu->is_completed ? 'btn-outline-secondary' : 'btn-outline-success' }}">
                                                        <i class="bi {{ $fu->is_completed ? 'bi-arrow-counterclockwise' : 'bi-check-circle' }} me-1"></i>
                                                        {{ $fu->is_completed ? 'Reopen' : 'Mark Complete' }}
                                                    </button>
                                                </form>

                                                <a href="{{ route('follow-ups.edit', [$serviceRequest, $fu]) }}"
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-pencil me-1"></i>Edit
                                                </a>

                                                <form action="{{ route('follow-ups.destroy', [$serviceRequest, $fu]) }}" method="POST"
                                                      onsubmit="return confirm('Remove this step from the timeline?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>

                                                <span class="text-muted ms-auto" style="font-size:.7rem">
                                                    by {{ $fu->creator->name }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>{{-- /timeline card --}}

        </div>{{-- /col-lg-8 --}}

        {{-- ── RIGHT COLUMN: Summary + Details ─────────────────── --}}
        <div class="col-lg-4">

            {{-- Current Status Card --}}
            <div class="page-card mb-4">
                <h6 class="fw-bold mb-3 text-uppercase text-muted" style="font-size:.72rem;letter-spacing:.07em">
                    <i class="bi bi-broadcast me-1"></i>Current Status
                </h6>
                @if($currentFollowUp)
                    @php $cfg = $currentFollowUp->statusConfig(); @endphp
                    <div class="d-flex align-items-center gap-3 p-3 rounded-3 bg-light border">
                        <div class="tl-marker-lg tl-marker-current-lg text-{{ $cfg['color'] }}">
                            <i class="bi {{ $cfg['icon'] }}" style="font-size:1.5rem"></i>
                        </div>
                        <div>
                            <div class="fw-bold">{{ $currentFollowUp->title }}</div>
                            <div class="small text-muted">{{ $cfg['label'] }}</div>
                            @if($currentFollowUp->scheduled_at)
                                <div class="small text-muted mt-1">
                                    <i class="bi bi-calendar-event me-1"></i>{{ $currentFollowUp->scheduled_at->format('d M Y') }}
                                </div>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="text-center py-3 text-muted small">
                        <i class="bi bi-check-circle-fill text-success me-1" style="font-size:1.2rem"></i><br>
                        All steps completed!
                    </div>
                @endif

                {{-- Progress bar --}}
                @php
                    $total     = $allFollowUps->count();
                    $done      = $allFollowUps->where('is_completed', true)->count();
                    $pct       = $total > 0 ? round(($done / $total) * 100) : 0;
                @endphp
                @if($total > 0)
                    <div class="mt-3">
                        <div class="d-flex justify-content-between text-muted mb-1" style="font-size:.72rem">
                            <span>Progress</span>
                            <span>{{ $done }} / {{ $total }} steps</span>
                        </div>
                        <div class="progress" style="height:6px;border-radius:3px">
                            <div class="progress-bar bg-success" style="width:{{ $pct }}%"></div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Next Steps Card --}}
            @php $upcoming = $allFollowUps->where('is_completed', false)->skip($currentFollowUp ? 1 : 0)->take(3); @endphp
            @if($upcoming->count())
            <div class="page-card mb-4">
                <h6 class="fw-bold mb-3 text-uppercase text-muted" style="font-size:.72rem;letter-spacing:.07em">
                    <i class="bi bi-arrow-right-circle me-1"></i>Coming Up
                </h6>
                <div class="d-flex flex-column gap-2">
                    @foreach($upcoming as $step)
                        @php $cfg = $step->statusConfig(); @endphp
                        <div class="d-flex align-items-start gap-2 p-2 rounded-3 bg-light border">
                            <i class="bi {{ $cfg['icon'] }} text-{{ $cfg['color'] }} mt-1" style="font-size:.9rem;flex-shrink:0"></i>
                            <div>
                                <div class="small fw-500">{{ $step->title }}</div>
                                @if($step->scheduled_at)
                                    <div class="text-muted" style="font-size:.7rem">
                                        <i class="bi bi-calendar-event me-1"></i>{{ $step->scheduled_at->format('d M Y') }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Request Details (collapsible) --}}
            <div class="page-card mb-4">
                <a class="d-flex justify-content-between align-items-center text-decoration-none text-dark fw-bold"
                   data-bs-toggle="collapse" href="#requestDetails" role="button">
                    <span style="font-size:.72rem;text-transform:uppercase;letter-spacing:.07em">
                        <i class="bi bi-file-text me-1 text-muted"></i>Request Details
                    </span>
                    <i class="bi bi-chevron-down text-muted small"></i>
                </a>
                <div class="collapse mt-3" id="requestDetails">

                    {{-- Service Type + Status --}}
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <div class="text-muted" style="font-size:.7rem;text-transform:uppercase;letter-spacing:.05em">Service Type</div>
                            <div class="small fw-500">{{ $serviceRequest->serviceType->name }}</div>
                        </div>
                        <div class="col-6">
                            <div class="text-muted" style="font-size:.7rem;text-transform:uppercase;letter-spacing:.05em">Status</div>
                            <span class="badge {{ $badgeClass }} small">{{ $badgeLabel }}</span>
                        </div>
                    </div>

                    {{-- Description --}}
                    <div class="mb-3">
                        <div class="text-muted" style="font-size:.7rem;text-transform:uppercase;letter-spacing:.05em;margin-bottom:.25rem">Description</div>
                        <div class="bg-light rounded-3 p-2 small" style="white-space:pre-wrap;line-height:1.6">{{ $serviceRequest->description }}</div>
                    </div>

                    {{-- Travel Info --}}
                    @if($serviceRequest->client_country || $serviceRequest->destination_country || $serviceRequest->travel_date_start)
                    <div class="border-top pt-3 mb-3">
                        <div class="text-muted mb-2" style="font-size:.7rem;text-transform:uppercase;letter-spacing:.05em">
                            <i class="bi bi-airplane me-1"></i>Travel Info
                        </div>
                        <div class="row g-2">
                            @if($serviceRequest->client_country)
                            <div class="col-6">
                                <div class="text-muted" style="font-size:.7rem">From</div>
                                <div class="small fw-500">{{ $serviceRequest->client_country }}</div>
                            </div>
                            @endif
                            @if($serviceRequest->destination_country)
                            <div class="col-6">
                                <div class="text-muted" style="font-size:.7rem">To</div>
                                <div class="small fw-500">
                                    {{ $serviceRequest->destination_country }}
                                    @if($serviceRequest->destination_city), {{ $serviceRequest->destination_city }}@endif
                                </div>
                            </div>
                            @endif
                            @if($serviceRequest->travel_date_start)
                            <div class="col-6">
                                <div class="text-muted" style="font-size:.7rem">Departure</div>
                                <div class="small fw-500">{{ $serviceRequest->travel_date_start->format('d M Y') }}</div>
                            </div>
                            @endif
                            @if($serviceRequest->travel_date_end)
                            <div class="col-6">
                                <div class="text-muted" style="font-size:.7rem">Return</div>
                                <div class="small fw-500">{{ $serviceRequest->travel_date_end->format('d M Y') }}</div>
                            </div>
                            @endif
                            @if($serviceRequest->durationDays() !== null)
                            <div class="col-6">
                                <div class="text-muted" style="font-size:.7rem">Duration</div>
                                <div class="small fw-bold text-primary">{{ $serviceRequest->durationDays() }} days</div>
                            </div>
                            @endif
                            @if($serviceRequest->companions_count)
                            <div class="col-6">
                                <div class="text-muted" style="font-size:.7rem">Companions</div>
                                <div class="small fw-500">{{ $serviceRequest->companions_count }}</div>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    {{-- Attachments --}}
                    @if($serviceRequest->attachments->count() || $serviceRequest->attachment_path)
                    <div class="border-top pt-3">
                        <div class="text-muted mb-2" style="font-size:.7rem;text-transform:uppercase;letter-spacing:.05em">
                            <i class="bi bi-paperclip me-1"></i>Attachments
                        </div>
                        @if($serviceRequest->attachments->count())
                            @foreach($serviceRequest->attachments as $att)
                            <div class="d-flex align-items-center gap-2 p-2 bg-light rounded-3 border mb-1">
                                <i class="bi bi-file-earmark text-primary"></i>
                                <div class="flex-grow-1 min-w-0">
                                    <div class="small text-truncate fw-500">{{ $att->original_name }}</div>
                                    <div class="text-muted" style="font-size:.7rem">{{ $att->humanSize() }}</div>
                                </div>
                                <a href="{{ $att->url() }}" target="_blank" class="btn btn-outline-primary btn-sm btn-action">
                                    <i class="bi bi-download"></i>
                                </a>
                            </div>
                            @endforeach
                        @elseif($serviceRequest->attachment_path)
                            <a href="{{ asset('storage/'.$serviceRequest->attachment_path) }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-download me-1"></i>Download File
                            </a>
                        @endif
                    </div>
                    @endif

                    {{-- Notes --}}
                    @if($serviceRequest->additional_notes)
                    <div class="border-top pt-3 mt-3">
                        <div class="text-muted mb-1" style="font-size:.7rem;text-transform:uppercase;letter-spacing:.05em">Notes</div>
                        <div class="small text-muted" style="white-space:pre-wrap;line-height:1.6">{{ $serviceRequest->additional_notes }}</div>
                    </div>
                    @endif
                </div>
            </div>

        </div>{{-- /col-lg-4 --}}
    </div>{{-- /row --}}

</div>
</div>

{{-- ── Add Follow-Up Modal (employees only) ────────────────────── --}}
@if($canManage)
<div class="modal fade" id="addFollowUpModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-plus-circle text-primary me-2"></i>Add Timeline Step
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('follow-ups.store', $serviceRequest) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label">Step Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" required
                                   placeholder="e.g. Visa Application Submitted">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Type <span class="text-danger">*</span></label>
                            <select name="status_type" class="form-select" required>
                                @foreach(\App\Models\FollowUp::STATUS_TYPES as $key => $cfg)
                                    <option value="{{ $key }}">{{ $cfg['label'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" rows="3" class="form-control"
                                      placeholder="Describe this step in clear, simple language…"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Scheduled Date <span class="text-muted fw-normal">(optional)</span></label>
                            <input type="date" name="scheduled_at" class="form-control">
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <div class="form-check mb-2">
                                <input type="checkbox" name="is_visible_to_client" id="vis_create"
                                       class="form-check-input" value="1" checked>
                                <label class="form-check-label" for="vis_create">
                                    Visible to client
                                </label>
                                <div class="form-text">Uncheck to keep this step internal.</div>
                            </div>
                        </div>

                        {{-- Optional extra data --}}
                        <div class="col-12">
                            <a class="small text-muted text-decoration-none" data-bs-toggle="collapse" href="#extraDataSection">
                                <i class="bi bi-plus me-1"></i>Add structured details (location, booking ref…)
                            </a>
                            <div class="collapse mt-2" id="extraDataSection">
                                <div class="bg-light rounded-3 p-3 border">
                                    <div class="row g-2">
                                        <div class="col-md-6">
                                            <label class="form-label small">Location</label>
                                            <input type="text" name="extra_data[location]" class="form-control form-control-sm"
                                                   placeholder="e.g. Embassy, Riyadh">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small">Reference / Booking No.</label>
                                            <input type="text" name="extra_data[reference]" class="form-control form-control-sm"
                                                   placeholder="e.g. TK-1234567">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small">Contact Person</label>
                                            <input type="text" name="extra_data[contact]" class="form-control form-control-sm"
                                                   placeholder="e.g. Agent Sara">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small">Additional Info</label>
                                            <input type="text" name="extra_data[note]" class="form-control form-control-sm"
                                                   placeholder="Any extra detail">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-plus-circle me-1"></i>Add to Timeline
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@endsection
