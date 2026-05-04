<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FollowUp extends Model
{
    protected $fillable = [
        'service_request_id', 'title', 'description', 'status_type',
        'scheduled_at', 'is_completed', 'completed_at',
        'created_by', 'is_visible_to_client', 'extra_data',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at'        => 'datetime',
            'completed_at'        => 'datetime',
            'is_completed'        => 'boolean',
            'is_visible_to_client'=> 'boolean',
            'extra_data'          => 'array',
        ];
    }

    const STATUS_TYPES = [
        'received'              => ['label' => 'Request Received',       'icon' => 'bi-inbox-fill',           'color' => 'primary'],
        'under_review'          => ['label' => 'Under Review',           'icon' => 'bi-eye-fill',             'color' => 'info'],
        'documents_required'    => ['label' => 'Documents Required',     'icon' => 'bi-file-earmark-text',    'color' => 'warning'],
        'processing'            => ['label' => 'Processing',             'icon' => 'bi-gear-fill',            'color' => 'primary'],
        'appointment_scheduled' => ['label' => 'Appointment Scheduled',  'icon' => 'bi-calendar-check-fill', 'color' => 'info'],
        'tickets_booked'        => ['label' => 'Tickets Booked',         'icon' => 'bi-ticket-perforated-fill','color' => 'success'],
        'visa_submitted'        => ['label' => 'Visa Submitted',         'icon' => 'bi-send-fill',            'color' => 'primary'],
        'visa_approved'         => ['label' => 'Visa Approved',          'icon' => 'bi-patch-check-fill',     'color' => 'success'],
        'completed'             => ['label' => 'Completed',              'icon' => 'bi-check-circle-fill',    'color' => 'success'],
        'update'                => ['label' => 'General Update',         'icon' => 'bi-info-circle-fill',     'color' => 'secondary'],
        'note'                  => ['label' => 'Internal Note',          'icon' => 'bi-sticky-fill',          'color' => 'warning'],
    ];

    public function serviceRequest()
    {
        return $this->belongsTo(ServiceRequest::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by')->withDefault(['name' => 'System']);
    }

    public function statusConfig(): array
    {
        return self::STATUS_TYPES[$this->status_type]
            ?? ['label' => ucfirst($this->status_type), 'icon' => 'bi-circle', 'color' => 'secondary'];
    }

    public function isPast(): bool
    {
        return $this->is_completed;
    }

    public function isFuture(): bool
    {
        return ! $this->is_completed;
    }
}
