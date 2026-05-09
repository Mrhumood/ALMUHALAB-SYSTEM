<?php

namespace App\Notifications;

use App\Models\ServiceRequest;
use App\Models\StageComment;
use App\Models\User;
use App\Services\WorkflowService;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CommentAddedNotification extends Notification
{
    public function __construct(
        public ServiceRequest $serviceRequest,
        public StageComment $comment,
        public User $actor,
    ) {}

    public function via($notifiable): array
    {
        return $notifiable->notificationChannels();
    }

    public function toDatabase($notifiable): array
    {
        $stageName = $this->comment->stage_number
            ? (WorkflowService::STAGES[$this->comment->stage_number]['label'] ?? 'Unknown Stage')
            : 'General';

        return [
            'title'              => "New comment on your request",
            'message'            => "\"{$this->actor->name}\" commented at {$stageName} stage",
            'preview'            => \Str::limit($this->comment->content, 80),
            'actor_name'         => $this->actor->name,
            'service_request_id' => $this->serviceRequest->id,
            'url'                => route('service-requests.show', $this->serviceRequest) . '#comments',
            'icon'               => 'bi-chat-left-text',
            'color'              => 'info',
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        $url = route('service-requests.show', $this->serviceRequest) . '#comments';

        return (new MailMessage)
            ->subject("New comment — {$this->serviceRequest->request_number} — ALMuhalab")
            ->greeting('Hello ' . ($notifiable->name ?? 'there') . ',')
            ->line("{$this->actor->name} commented on request **#{$this->serviceRequest->request_number}**.")
            ->line("**{$this->serviceRequest->title}**")
            ->line('"' . \Str::limit($this->comment->content, 150) . '"')
            ->action('View Comment', $url)
            ->line('ALMuhalab International Co. — Kuwait City');
    }

    public function toWhatsApp($notifiable): string
    {
        $url     = route('service-requests.show', $this->serviceRequest);
        $preview = \Str::limit($this->comment->content, 120);

        return "💬 *ALMuhalab — New Comment*\n\n"
            . "Request: *{$this->serviceRequest->request_number}*\n"
            . "{$this->serviceRequest->title}\n\n"
            . "*{$this->actor->name}* said:\n_{$preview}_\n\n"
            . "🔗 {$url}";
    }
}
