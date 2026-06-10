<?php

namespace App\Notifications\Admin;

use Illuminate\Notifications\Notification;

class NewFeedbackSubmitted extends Notification
{
    public function __construct(
        public readonly int    $feedbackId,
        public readonly string $judul,
        public readonly string $pengirim,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'        => 'new_feedback',
            'feedback_id' => $this->feedbackId,
            'judul'       => $this->judul,
            'message'     => "Feedback baru dari {$this->pengirim}: \"{$this->judul}\".",
            'url'         => route('admin.feedback.show', $this->feedbackId),
        ];
    }
}
