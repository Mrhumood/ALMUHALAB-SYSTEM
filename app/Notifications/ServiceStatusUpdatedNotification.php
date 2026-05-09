<?php

namespace App\Notifications;

use App\Models\RequestService;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ServiceStatusUpdatedNotification extends Notification
{
    public function __construct(
        public ServiceRequest $serviceRequest,
        public RequestService $requestService,
        public string $oldStatus,
        public User $actor,
    ) {}

    public function via($notifiable): array
    {
        return $notifiable->notificationChannels();
    }

    public function toDatabase($notifiable): array
    {
        $cfg  = $this->requestService->statusConfig();
        $name = $this->requestService->service->name ?? 'Service';
        $old  = RequestService::STATUSES[$this->oldStatus]['label'] ?? $this->oldStatus;
        $new  = $cfg['label'];

        return [
            'title'              => "Service status updated: {$name}",
            'message'            => "#{$this->serviceRequest->id} — {$old} → {$new}",
            'action'             => 'service_status_updated',
            'service_name'       => $name,
            'old_status'         => $this->oldStatus,
            'new_status'         => $this->requestService->status,
            'actor_name'         => $this->actor->name,
            'service_request_id' => $this->serviceRequest->id,
            'url'                => route('service-requests.show', $this->serviceRequest),
            'icon'               => $cfg['icon'],
            'color'              => $cfg['color'],
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        $cfg  = $this->requestService->statusConfig();
        $name = $this->requestService->service->name ?? 'Service';
        $old  = RequestService::STATUSES[$this->oldStatus]['label'] ?? $this->oldStatus;
        $new  = $cfg['label'];
        $url  = route('service-requests.show', $this->serviceRequest);

        return (new MailMessage)
            ->subject("Service update: {$name} — ALMuhalab")
            ->greeting('Hello ' . ($notifiable->name ?? 'there') . ',')
            ->line("A service on request **#{$this->serviceRequest->request_number}** has been updated.")
            ->line("Service: **{$name}**")
            ->line("Status: {$old} → **{$new}**")
            ->line("Updated by: {$this->actor->name}")
            ->action('View Request', $url)
            ->line('ALMuhalab International Co. — Kuwait City');
    }

    public function toWhatsApp($notifiable): string
    {
        $cfg  = $this->requestService->statusConfig();
        $name = $this->requestService->service->name ?? 'Service';
        $new  = $cfg['label'];
        $url  = route('service-requests.show', $this->serviceRequest);

        return "🔧 *ALMuhalab — Service Update*\n\n"
            . "Request: *{$this->serviceRequest->request_number}*\n"
            . "{$this->serviceRequest->title}\n\n"
            . "Service: {$name}\n"
            . "New status: *{$new}*\n\n"
            . "🔗 {$url}";
    }
}
