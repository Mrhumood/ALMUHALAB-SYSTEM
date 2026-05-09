{{--
    Params:
      $field    — field key (e.g. 'description')
      $sr       — ServiceRequest model
      $map      — fieldVisibilityMap array
      $allRoles — collection of Role models
--}}
@if($canManageFields)
@php
    $rule     = $map[$field] ?? null;
    $vis      = $rule['visibility'] ?? 'all';
    $roleVal  = $rule['required_permission'] ?? '';
    $selected = ($vis === 'admin' && $roleVal) ? $roleVal : 'all';
    $vcfg     = \App\Models\RequestFieldVisibility::VISIBILITY[$vis];
    if ($vis === 'admin' && $roleVal) {
        $vcfg['label'] = $roleVal;
    }
@endphp
<div class="dropdown ms-1 flex-shrink-0">
    <button type="button"
            class="btn btn-sm btn-link p-0 text-{{ $vcfg['color'] }} opacity-75"
            style="font-size:.8rem" data-bs-toggle="dropdown" aria-expanded="false"
            title="{{ $vcfg['label'] }}">
        <i class="bi {{ $vcfg['icon'] }}"></i>
    </button>
    <div class="dropdown-menu shadow p-2" style="min-width:200px;font-size:.82rem">
        <div class="text-muted mb-2 fw-semibold" style="font-size:.72rem;text-transform:uppercase">
            {{ \App\Models\RequestFieldVisibility::FIELDS[$field] ?? $field }}
        </div>
        <form action="{{ route('service-requests.fields.visibility', [$sr, $field]) }}"
              method="POST">
            @csrf @method('PATCH')
            <select name="visibility" class="form-select form-select-sm mb-2">
                <option value="all" {{ $selected === 'all' ? 'selected' : '' }}>{{ __('Everyone') }}</option>
                @foreach($allRoles as $role)
                    <option value="{{ $role->name }}" {{ $selected === $role->name ? 'selected' : '' }}>
                        {{ $role->name }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-primary btn-sm w-100 py-0">
                {{ __('Save') }}
            </button>
        </form>
    </div>
</div>
@endif
