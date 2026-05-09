<?php

namespace App\Notifications;

use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AssignedToRequestNotification extends Notification
{
    public function __construct(
        public ServiceRequest $serviceRequest,
        public User $assignedBy,
    ) {}

    public function via($notifiable): array
    {
        return $notifiable->notificationChannels();
    }

    public function toDatabase($notifiable): array
    {
        return [
            'title'              => "You have been assigned to a request",
            'message'            => "#{$this->serviceRequest->id} — {$this->serviceRequest->title}",
            'action'             => 'assigned',
            'actor_name'         => $this->assignedBy->name,
            'service_request_id' => $this->serviceRequest->id,
            'url'                => route('service-requests.show', $this->serviceRequest),
            'icon'               => 'bi-person-check-fill',
            'color'              => 'primary',
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        $url = route('service-requests.show', $this->serviceRequest);

        return (new MailMessage)
            ->subject("Assigned to request: {$this->serviceRequest->request_number} — ALMuhalab")
            ->greeting('Hello ' . ($notifiable->name ?? 'there') . ',')
            ->line("You have been assigned to request **#{$this->serviceRequest->request_number}**.")
            ->line("**{$this->serviceRequest->title}**")
            ->line("Assigned by: {$this->assignedBy->name}")
            ->action('Open Request', $url)
            ->line('ALMuhalab International Co. — Kuwait City');
    }

    public function toWhatsApp($notifiable): string
    {
        $url = route('service-requests.show', $this->serviceRequest);

        return "👤 *ALMuhalab — Assigned to You*\n\n"
            . "Request: *{$this->serviceRequest->request_number}*\n"
            . "{$this->serviceRequest->title}\n\n"
            . "Assigned by: {$this->assignedBy->name}\n\n"
            . "🔗 {$url}";
    }
}
