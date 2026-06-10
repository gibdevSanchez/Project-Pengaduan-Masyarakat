<?php

namespace App\Notifications\Petugas;

use Illuminate\Notifications\Notification;

class NewFeedbackAssigned extends Notification
{
    public function __construct(
        public readonly int    $feedbackId,
        public readonly string $judul,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'        => 'feedback_assigned',
            'feedback_id' => $this->feedbackId,
            'judul'       => $this->judul,
            'message'     => "Feedback \"{$this->judul}\" telah ditugaskan kepada Anda.",
            'url'         => route('petugas.feedback.index'),
        ];
    }
}
