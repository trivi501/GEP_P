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
        return ['database'];
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
