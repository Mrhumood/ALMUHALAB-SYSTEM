<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppChannel
{
    public function send($notifiable, Notification $notification): void
    {
        if (! method_exists($notification, 'toWhatsApp')) {
            return;
        }

        $number = $notifiable->whatsapp_number ?? $notifiable->phone_number;
        if (! $number) {
            return;
        }

        $message = $notification->toWhatsApp($notifiable);
        if (empty($message)) {
            return;
        }

        $sid   = config('services.twilio.sid');
        $token = config('services.twilio.token');
        $from  = config('services.twilio.whatsapp_from'); // e.g. whatsapp:+14155238886

        if (! $sid || ! $token || ! $from) {
            Log::warning('WhatsApp notification skipped: Twilio credentials not configured.');
            return;
        }

        // Normalize number: ensure it starts with + and has country code
        $to = preg_replace('/\s+/', '', $number);
        if (! str_starts_with($to, '+')) {
            $to = '+' . $to;
        }

        try {
            $response = Http::withBasicAuth($sid, $token)
                ->asForm()
                ->post("https://api.twilio.com/2010-04-01/Accounts/{$sid}/Messages.json", [
                    'From' => $from,
                    'To'   => "whatsapp:{$to}",
                    'Body' => $message,
                ]);

            if (! $response->successful()) {
                Log::error('WhatsApp send failed', ['status' => $response->status(), 'body' => $response->body()]);
            }
        } catch (\Exception $e) {
            Log::error('WhatsApp send exception: ' . $e->getMessage());
        }
    }
}
