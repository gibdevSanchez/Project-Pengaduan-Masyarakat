<?php

namespace App\Notifications\Petugas;

use Illuminate\Notifications\Notification;

class AdminChangedData extends Notification
{
    public function __construct(
        public readonly string $konteks,
        public readonly string $adminNama,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'    => 'admin_changed_data',
            'message' => "Admin {$this->adminNama} mengubah {$this->konteks} akun Anda.",
            'url'     => route('petugas.profil'),
        ];
    }
}
