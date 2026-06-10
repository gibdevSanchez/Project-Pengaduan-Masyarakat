<?php

namespace App\Notifications\Masyarakat;

use Illuminate\Notifications\Notification;

class PengaduanTakedown extends Notification
{
    public function __construct(
        public readonly int    $pengaduanId,
        public readonly string $judul,
        public readonly string $alasan,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'         => 'pengaduan_takedown',
            'pengaduan_id' => $this->pengaduanId,
            'judul'        => $this->judul,
            'message'      => "Pengaduan \"{$this->judul}\" dinyatakan tidak valid. Alasan: {$this->alasan}",
            'url'          => route('masyarakat.pengaduan.show', $this->pengaduanId),
        ];
    }
}
