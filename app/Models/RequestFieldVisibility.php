<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestFieldVisibility extends Model
{
    protected $table = 'request_field_visibility';

    protected $fillable = [
        'service_request_id', 'field_name', 'visibility', 'required_permission',
    ];

    /**
     * Human-readable labels for each controllable field.
     */
    const FIELDS = [
        'client_info'      => 'Client Information',
        'description'      => 'Description',
        'travel_info'      => 'Travel Information',
        'companions_count' => 'Companions',
        'additional_notes' => 'Additional Notes',
    ];

    const VISIBILITY = [
        'all' => [
            'label' => 'Everyone',
            'color' => 'success',
            'icon'  => 'bi-eye-fill',
        ],
        'employee' => [
            'label' => 'Staff Only',
            'color' => 'primary',
            'icon'  => 'bi-people-fill',
        ],
        'admin' => [
            'label' => 'Restricted',
            'color' => 'danger',
            'icon'  => 'bi-shield-lock-fill',
        ],
    ];

    public function serviceRequest()
    {
        return $this->belongsTo(ServiceRequest::class);
    }
}
