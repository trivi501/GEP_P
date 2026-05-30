<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;

class WhatsAppChannel
{
    public function send(object $notifiable, Notification $notification): void
    {
        $message = $notification->toWhatsApp($notifiable);

        if (! $message instanceof WhatsAppMessage) {
            return;
        }

        $phone = $notifiable->phone?->value ?? $notifiable->phone;

        if (! $phone) {
            return;
        }

        $phoneNumberId = config('services.whatsapp.phone_number_id');
        $token = config('services.whatsapp.token');

        $payload = [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $phone,
        ];

        if ($message->templateName) {
            $payload['type'] = 'template';
            $payload['template'] = [
                'name' => $message->templateName,
                'language' => ['code' => 'es_MX'],
            ];

            if (! empty($message->templateParameters)) {
                $components = [];
                $headerParams = [];
                $bodyParams = [];

                foreach ($message->templateParameters as $index => $param) {
                    $bodyParams[] = ['type' => 'text', 'text' => $param];
                }

                if ($headerParams) {
                    $components[] = [
                        'type' => 'header',
                        'parameters' => $headerParams,
                    ];
                }

                if ($bodyParams) {
                    array_unshift($components, [
                        'type' => 'body',
                        'parameters' => $bodyParams,
                    ]);
                }

                $payload['template']['components'] = $components;
            }
        } else {
            $payload['type'] = 'text';
            $payload['text'] = ['body' => $message->content];
        }

        Http::withToken($token)
            ->post("https://graph.facebook.com/v22.0/{$phoneNumberId}/messages", $payload);
    }
}
