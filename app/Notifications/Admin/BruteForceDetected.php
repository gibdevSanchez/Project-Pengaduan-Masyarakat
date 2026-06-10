<?php

namespace App\Notifications\Admin;

use Illuminate\Notifications\Notification;

class BruteForceDetected extends Notification
{
    public function __construct(
        public readonly string $ip,
        public readonly string $guard,
        public readonly int    $attempts,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'     => 'brute_force',
            'ip'       => $this->ip,
            'message'  => "Terdeteksi {$this->attempts}x percobaan login gagal dari IP {$this->ip} (guard: {$this->guard}).",
            'url'      => route('admin.utilitas'),
        ];
    }
}
