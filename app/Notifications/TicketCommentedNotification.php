<?php

namespace App\Notifications;

use App\Models\SupportTicket;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TicketCommentedNotification extends Notification
{
    use Queueable;

    public function __construct(public SupportTicket $ticket, public string $comment, public string $commenterName)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'ticket_id' => $this->ticket->id,
            'title' => $this->ticket->title,
            'message' => "{$this->commenterName} comentó en el ticket: {$this->ticket->title}",
            'comment' => $this->comment,
        ];
    }
}
