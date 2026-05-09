{{--
    Params:
      $field  — field key
      $label  — display label
      $sr     — ServiceRequest model
      $map    — fieldVisibilityMap array
--}}
@if($canManageFields)
@php
    $rule     = $map[$field] ?? null;
    $vis      = $rule['visibility'] ?? 'all';
    $roleName = $rule['required_permission'] ?? null;
    $restrictLabel = match(true) {
        $vis === 'admin' && $roleName => __('Visible to') . ': ' . $roleName,
        $vis === 'employee'           => __('Staff Only'),
        default                       => __('Restricted'),
    };
@endphp
<div class="d-flex align-items-center justify-content-between py-1 px-2 rounded mb-1"
     style="background:rgba(220,53,69,.06);border:1px dashed rgba(220,53,69,.25)">
    <span class="small text-muted fst-italic">
        <i class="bi bi-eye-slash me-1 text-danger"></i>
        {{ $label }} — <span class="text-danger" style="font-size:.75rem">{{ $restrictLabel }}</span>
    </span>
    @include('service_requests._field_visibility_toggle', ['field' => $field, 'sr' => $sr, 'map' => $map])
</div>
@endif
