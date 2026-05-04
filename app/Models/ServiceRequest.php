<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceRequest extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'service_type_id',
        'title', 'description', 'status',
        'attachment_path',
        'client_country', 'destination_country', 'destination_city',
        'travel_date_start', 'travel_date_end',
        'companions_count', 'additional_notes',
    ];

    protected function casts(): array
    {
        return [
            'travel_date_start' => 'date',
            'travel_date_end'   => 'date',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withDefault(['name' => 'Unknown']);
    }

    public function serviceType()
    {
        return $this->belongsTo(ServiceType::class)->withDefault(['name' => '—']);
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class);
    }

    public function durationDays(): ?int
    {
        if ($this->travel_date_start && $this->travel_date_end) {
            return $this->travel_date_start->diffInDays($this->travel_date_end);
        }
        return null;
    }

    public function followUps()
    {
        return $this->hasMany(FollowUp::class)
                    ->orderBy('scheduled_at')
                    ->orderBy('created_at');
    }

    public function requestServices()
    {
        return $this->hasMany(RequestService::class)->with('service')->orderBy('created_at');
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class, 'subject_id')
                    ->where('subject_type', self::class);
    }
}
