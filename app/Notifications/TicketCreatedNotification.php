<?php

namespace App\Notifications;

use App\Models\SupportTicket;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TicketCreatedNotification extends Notification
{
    use Queueable;

    public function __construct(public SupportTicket $ticket)
    {
    }

    public function via(object $notifiable): array
    {
        $channels = ['database'];

        if ($notifiable->phone) {
            $channels[] = WhatsAppChannel::class;
        }

        return $channels;
    }

    public function toWhatsApp(object $notifiable): WhatsAppMessage
    {
        return WhatsAppMessage::create()
            ->content("🔔 Nuevo ticket de soporte\n\nDe: {$this->ticket->user?->name ?? 'Usuario'}\nAsunto: {$this->ticket->title}\nPrioridad: {$this->ticket->priority}\n\nID: #{$this->ticket->id}");
    }

    public function toArray(object $notifiable): array
    {
        return [
            'ticket_id' => $this->ticket->id,
            'title' => $this->ticket->title,
            'user' => $this->ticket->user?->name ?? 'Usuario',
            'message' => "Nuevo ticket de soporte: {$this->ticket->title}",
        ];
    }
}
