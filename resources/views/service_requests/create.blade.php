@extends('layouts.app')
@section('title','New Request')

@section('content')
<div class="row justify-content-center">
<div class="col-lg-9">

    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ route('service-requests.index') }}">Requests</a></li>
            <li class="breadcrumb-item active">New Request</li>
        </ol>
    </nav>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible d-flex align-items-center gap-2 mb-3">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <div>Please fix the errors below before submitting.</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('service-requests.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    {{-- ── Section 1: Basic Info ─────────────────────────── --}}
    <div class="page-card mb-4">
        <h6 class="fw-bold mb-4 text-uppercase text-muted" style="font-size:.72rem;letter-spacing:.07em">
            <i class="bi bi-file-text me-1"></i>Request Info
        </h6>

        <div class="row g-3">
            <div class="col-md-8">
                <label class="form-label">Title <span class="text-danger">*</span></label>
                <input type="text" name="title"
                       class="form-control @error('title') is-invalid @enderror"
                       value="{{ old('title') }}" placeholder="Brief title for this request">
                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="form-label">Service Type</label>
                <select name="service_type_id" class="form-select @error('service_type_id') is-invalid @enderror">
                    <option value="">— Select Type —</option>
                    @foreach($serviceTypes as $type)
                        <option value="{{ $type->id }}" {{ old('service_type_id') == $type->id ? 'selected' : '' }}>
                            {{ $type->name }}
                        </option>
                    @endforeach
                </select>
                @error('service_type_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12">
                <label class="form-label">Description <span class="text-danger">*</span></label>
                <textarea name="description" rows="4"
                          class="form-control @error('description') is-invalid @enderror"
                          placeholder="Describe your request in detail…">{{ old('description') }}</textarea>
                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            @if(auth()->user()->hasPermission('update_status'))
            <div class="col-md-4">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    @foreach(['New','Under Review','Approved','Rejected','Completed'] as $s)
                        <option value="{{ $s }}" {{ old('status','New') === $s ? 'selected':'' }}>{{ $s }}</option>
                    @endforeach
                </select>
            </div>
            @endif
        </div>
    </div>

    {{-- ── Section 2: Client & Travel Info ─────────────────── --}}
    <div class="page-card mb-4">
        <h6 class="fw-bold mb-4 text-uppercase text-muted" style="font-size:.72rem;letter-spacing:.07em">
            <i class="bi bi-airplane me-1"></i>Travel Information
        </h6>

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Client Country</label>
                <input type="text" name="client_country"
                       class="form-control @error('client_country') is-invalid @enderror"
                       value="{{ old('client_country') }}" placeholder="e.g. Saudi Arabia">
                @error('client_country')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">Destination Country</label>
                <input type="text" name="destination_country"
                       class="form-control @error('destination_country') is-invalid @enderror"
                       value="{{ old('destination_country') }}" placeholder="e.g. Germany">
                @error('destination_country')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">Destination City <span class="text-muted fw-normal">(optional)</span></label>
                <input type="text" name="destination_city"
                       class="form-control @error('destination_city') is-invalid @enderror"
                       value="{{ old('destination_city') }}" placeholder="e.g. Berlin">
                @error('destination_city')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-2">
                <label class="form-label">Companions</label>
                <input type="number" name="companions_count" min="0" max="99"
                       class="form-control @error('companions_count') is-invalid @enderror"
                       value="{{ old('companions_count', 0) }}">
                @error('companions_count')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="form-label">Travel Start Date</label>
                <input type="date" name="travel_date_start"
                       class="form-control @error('travel_date_start') is-invalid @enderror"
                       value="{{ old('travel_date_start') }}">
                @error('travel_date_start')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="form-label">Travel End Date</label>
                <input type="date" name="travel_date_end"
                       class="form-control @error('travel_date_end') is-invalid @enderror"
                       value="{{ old('travel_date_end') }}">
                @error('travel_date_end')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <div class="w-100 bg-light rounded-3 p-2 text-center border" id="duration-display">
                    <div class="text-muted small">Duration</div>
                    <div class="fw-bold" id="duration-val">—</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Section 3: Notes & Attachments ──────────────────── --}}
    <div class="page-card mb-4">
        <h6 class="fw-bold mb-4 text-uppercase text-muted" style="font-size:.72rem;letter-spacing:.07em">
            <i class="bi bi-paperclip me-1"></i>Notes & Attachments
        </h6>

        <div class="row g-3">
            <div class="col-12">
                <label class="form-label">Additional Notes <span class="text-muted fw-normal">(optional)</span></label>
                <textarea name="additional_notes" rows="3"
                          class="form-control @error('additional_notes') is-invalid @enderror"
                          placeholder="Any special requirements or notes…">{{ old('additional_notes') }}</textarea>
                @error('additional_notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12">
                <label class="form-label">Attachments <span class="text-muted fw-normal">(optional, multiple)</span></label>
                <input type="file" name="attachments[]" multiple
                       class="form-control @error('attachments') is-invalid @enderror">
                <div class="form-text"><i class="bi bi-info-circle me-1"></i>Max 5 MB per file. You can select multiple files.</div>
                @error('attachments')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                @error('attachments.*')<div class="text-danger small mt-1"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>@enderror
            </div>
        </div>
    </div>

    {{-- Actions --}}
    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary px-4">
            <i class="bi bi-check-circle me-1"></i>Submit Request
        </button>
        <a href="{{ route('service-requests.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </div>

    </form>
</div>
</div>

<script>
// Live duration calculation
const startInput = document.querySelector('[name="travel_date_start"]');
const endInput   = document.querySelector('[name="travel_date_end"]');
const durVal     = document.getElementById('duration-val');

function updateDuration() {
    const s = new Date(startInput.value);
    const e = new Date(endInput.value);
    if (startInput.value && endInput.value && e >= s) {
        const days = Math.round((e - s) / 86400000);
        durVal.textContent = days + ' day' + (days !== 1 ? 's' : '');
    } else {
        durVal.textContent = '—';
    }
}
startInput.addEventListener('change', updateDuration);
endInput.addEventListener('change', updateDuration);
</script>
@endsection
