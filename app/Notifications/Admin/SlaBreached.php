<?php

namespace App\Notifications\Admin;

use Illuminate\Notifications\Notification;

class SlaBreached extends Notification
{
    public function __construct(
        public readonly int    $pengaduanId,
        public readonly string $judul,
        public readonly string $petugasNama,
        public readonly int    $menitTerlambat,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $jam   = intdiv($this->menitTerlambat, 60);
        $menit = $this->menitTerlambat % 60;
        $lewat = $jam > 0 ? "{$jam}j {$menit}m" : "{$menit}m";

        return [
            'type'         => 'sla_breached',
            'pengaduan_id' => $this->pengaduanId,
            'judul'        => $this->judul,
            'message'      => "SLA pengaduan \"{$this->judul}\" (petugas: {$this->petugasNama}) telah terlewat {$lewat}!",
            'url'          => route('admin.pengaduan.index'),
        ];
    }
}
