<?php

namespace App\Notifications\Petugas;

use Illuminate\Notifications\Notification;

class SlaNearBreach extends Notification
{
    public function __construct(
        public readonly int    $pengaduanId,
        public readonly string $judul,
        public readonly int    $menitTersisa,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $jam   = intdiv($this->menitTersisa, 60);
        $menit = $this->menitTersisa % 60;
        $sisa  = $jam > 0 ? "{$jam}j {$menit}m" : "{$menit}m";

        return [
            'type'         => 'sla_near_breach',
            'pengaduan_id' => $this->pengaduanId,
            'judul'        => $this->judul,
            'message'      => "SLA pengaduan \"{$this->judul}\" akan habis dalam {$sisa}. Segera tindak lanjuti!",
            'url'          => route('petugas.dashboard'),
        ];
    }
}
