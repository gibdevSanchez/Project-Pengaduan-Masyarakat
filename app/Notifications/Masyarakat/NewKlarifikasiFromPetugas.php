<?php

namespace App\Notifications\Masyarakat;

use Illuminate\Notifications\Notification;

class NewKlarifikasiFromPetugas extends Notification
{
    public function __construct(
        public readonly int    $pengaduanId,
        public readonly string $judul,
        public readonly string $petugasNama,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'         => 'klarifikasi_petugas',
            'pengaduan_id' => $this->pengaduanId,
            'judul'        => $this->judul,
            'message'      => "Petugas {$this->petugasNama} mengirim pesan pada pengaduan \"{$this->judul}\".",
            'url'          => route('masyarakat.pengaduan.show', $this->pengaduanId),
        ];
    }
}
