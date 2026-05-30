<?php

namespace App\Notifications;

use App\Models\SupportTicket;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TicketAssignedNotification extends Notification
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
            ->content("📌 Ticket asignado\n\nHas sido asignado al ticket:\n{$this->ticket->title}\nPrioridad: {$this->ticket->priority}\n\nID: #{$this->ticket->id}");
    }

    public function toArray(object $notifiable): array
    {
        return [
            'ticket_id' => $this->ticket->id,
            'title' => $this->ticket->title,
            'message' => "Te han asignado el ticket: {$this->ticket->title}",
        ];
    }
}
