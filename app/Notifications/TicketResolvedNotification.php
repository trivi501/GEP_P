<?php

namespace App\Notifications;

use App\Models\SupportTicket;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TicketResolvedNotification extends Notification
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
        $statusText = $this->ticket->status === 'cerrado' ? 'cerrado' : 'resuelto';

        return WhatsAppMessage::create()
            ->content("✅ Ticket {$statusText}\n\nTu ticket ha sido {$statusText}:\n{$this->ticket->title}\n\nID: #{$this->ticket->id}");
    }

    public function toArray(object $notifiable): array
    {
        $statusText = $this->ticket->status === 'cerrado' ? 'cerrado' : 'resuelto';
        return [
            'ticket_id' => $this->ticket->id,
            'title' => $this->ticket->title,
            'message' => "Tu ticket ha sido {$statusText}: {$this->ticket->title}",
        ];
    }
}
