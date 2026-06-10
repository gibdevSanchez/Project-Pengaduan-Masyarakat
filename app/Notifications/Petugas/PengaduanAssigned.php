<?php

namespace App\Notifications\Petugas;

use Illuminate\Notifications\Notification;

class PengaduanAssigned extends Notification
{
    public function __construct(
        public readonly int    $pengaduanId,
        public readonly string $judul,
        public readonly string $kategori,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'         => 'pengaduan_assigned',
            'pengaduan_id' => $this->pengaduanId,
            'judul'        => $this->judul,
            'kategori'     => $this->kategori,
            'message'      => "Pengaduan \"{$this->judul}\" (kategori: {$this->kategori}) telah ditugaskan kepada Anda.",
            'url'          => route('petugas.dashboard'),
        ];
    }
}
