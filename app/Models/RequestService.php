<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestService extends Model
{
    protected $fillable = [
        'service_request_id', 'service_catalog_id',
        'status', 'scheduled_at', 'notes', 'reference', 'created_by',
    ];

    protected function casts(): array
    {
        return ['scheduled_at' => 'datetime'];
    }

    const STATUSES = [
        'pending'   => ['label' => 'Pending',    'color' => 'secondary', 'icon' => 'bi-clock'],
        'booked'    => ['label' => 'Booked',     'color' => 'primary',   'icon' => 'bi-calendar-check'],
        'completed' => ['label' => 'Completed',  'color' => 'success',   'icon' => 'bi-check-circle-fill'],
        'cancelled' => ['label' => 'Cancelled',  'color' => 'danger',    'icon' => 'bi-x-circle'],
    ];

    public function serviceRequest()
    {
        return $this->belongsTo(ServiceRequest::class);
    }

    public function service()
    {
        return $this->belongsTo(ServiceCatalog::class, 'service_catalog_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by')->withDefault(['name' => 'System']);
    }

    public function statusConfig(): array
    {
        return self::STATUSES[$this->status] ?? ['label' => $this->status, 'color' => 'secondary', 'icon' => 'bi-circle'];
    }
}
