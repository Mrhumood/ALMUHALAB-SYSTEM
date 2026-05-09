<?php

namespace App\Notifications;

use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StageStatusChangedNotification extends Notification
{
    public function __construct(
        public ServiceRequest $serviceRequest,
        public string $fromStatus,
        public string $toStatus,
        public User $actor,
    ) {}

    public function via($notifiable): array
    {
        return $notifiable->notificationChannels();
    }

    public function toDatabase($notifiable): array
    {
        $isRejected = $this->toStatus === 'Rejected';

        return [
            'title'              => $isRejected
                ? "Request rejected: {$this->serviceRequest->title}"
                : "Status updated to: {$this->toStatus}",
            'message'            => "#{$this->serviceRequest->id} — {$this->serviceRequest->title}",
            'from_status'        => $this->fromStatus,
            'to_status'          => $this->toStatus,
            'actor_name'         => $this->actor->name,
            'service_request_id' => $this->serviceRequest->id,
            'url'                => route('service-requests.show', $this->serviceRequest),
            'icon'               => $isRejected ? 'bi-x-circle' : 'bi-arrow-repeat',
            'color'              => $isRejected ? 'danger' : 'primary',
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        $isRejected = $this->toStatus === 'Rejected';
        $url        = route('service-requests.show', $this->serviceRequest);

        $subject = $isRejected
            ? "Request rejected — ALMuhalab"
            : "Status updated to {$this->toStatus} — ALMuhalab";

        $mail = (new MailMessage)
            ->subject($subject)
            ->greeting('Hello ' . ($notifiable->name ?? 'there') . ',');

        if ($isRejected) {
            $mail->line("Your request **#{$this->serviceRequest->request_number}** has been rejected.")
                 ->line("**{$this->serviceRequest->title}**")
                 ->line("Please contact the team for more information.");
        } else {
            $mail->line("Your request **#{$this->serviceRequest->request_number}** status has changed.")
                 ->line("**{$this->serviceRequest->title}**")
                 ->line("{$this->fromStatus} → **{$this->toStatus}**");
        }

        return $mail
            ->line("Updated by: {$this->actor->name}")
            ->action('View Request', $url)
            ->line('ALMuhalab International Co. — Kuwait City');
    }

    public function toWhatsApp($notifiable): string
    {
        $isRejected = $this->toStatus === 'Rejected';
        $url        = route('service-requests.show', $this->serviceRequest);
        $emoji      = $isRejected ? '❌' : '🔄';

        return "{$emoji} *ALMuhalab — Status Update*\n\n"
            . "Request: *{$this->serviceRequest->request_number}*\n"
            . "{$this->serviceRequest->title}\n\n"
            . "Status: {$this->fromStatus} → *{$this->toStatus}*\n"
            . "Updated by: {$this->actor->name}\n\n"
            . "🔗 {$url}";
    }
}
