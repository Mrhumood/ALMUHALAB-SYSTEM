<?php

namespace App\Notifications;

use App\Models\ServiceRequest;
use App\Models\User;
use App\Services\WorkflowService;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StageTransitionNotification extends Notification
{
    public function __construct(
        public ServiceRequest $serviceRequest,
        public int $fromStage,
        public int $toStage,
        public string $action,   // 'advanced' | 'returned'
        public User $actor,
    ) {}

    public function via($notifiable): array
    {
        return $notifiable->notificationChannels();
    }

    public function toDatabase($notifiable): array
    {
        $from = WorkflowService::STAGES[$this->fromStage]['label'] ?? "Stage {$this->fromStage}";
        $to   = WorkflowService::STAGES[$this->toStage]['label']   ?? "Stage {$this->toStage}";

        $title = match ($this->action) {
            'advanced' => "Request moved to: {$to}",
            'returned' => "Request returned to: {$to}",
            default    => "Request stage changed",
        };

        return [
            'title'              => $title,
            'message'            => "#{$this->serviceRequest->id} — {$this->serviceRequest->title}",
            'action'             => $this->action,
            'from_stage'         => $this->fromStage,
            'to_stage'           => $this->toStage,
            'actor_name'         => $this->actor->name,
            'service_request_id' => $this->serviceRequest->id,
            'url'                => route('service-requests.show', $this->serviceRequest),
            'icon'               => $this->action === 'returned' ? 'bi-arrow-left-circle' : 'bi-arrow-right-circle',
            'color'              => $this->action === 'returned' ? 'warning' : 'success',
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        $to  = WorkflowService::STAGES[$this->toStage]['label'] ?? "Stage {$this->toStage}";
        $url = route('service-requests.show', $this->serviceRequest);

        $subject = $this->action === 'returned'
            ? "Request returned to: {$to}"
            : "Request moved to: {$to}";

        return (new MailMessage)
            ->subject($subject . ' — ALMuhalab')
            ->greeting('Hello ' . ($notifiable->name ?? 'there') . ',')
            ->line("Your request **#{$this->serviceRequest->request_number}** has been updated.")
            ->line("**{$this->serviceRequest->title}**")
            ->line("Stage changed to: **{$to}**")
            ->line("Updated by: {$this->actor->name}")
            ->action('View Request', $url)
            ->line('ALMuhalab International Co. — Kuwait City');
    }

    public function toWhatsApp($notifiable): string
    {
        $to  = WorkflowService::STAGES[$this->toStage]['label'] ?? "Stage {$this->toStage}";
        $url = route('service-requests.show', $this->serviceRequest);

        return "📋 *ALMuhalab — Request Update*\n\n"
            . "Request: *{$this->serviceRequest->request_number}*\n"
            . "{$this->serviceRequest->title}\n\n"
            . "Stage changed to: *{$to}*\n"
            . "Updated by: {$this->actor->name}\n\n"
            . "🔗 {$url}";
    }
}
