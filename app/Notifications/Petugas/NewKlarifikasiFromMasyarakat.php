<?php

namespace App\Notifications\Petugas;

use Illuminate\Notifications\Notification;

class NewKlarifikasiFromMasyarakat extends Notification
{
    public function __construct(
        public readonly int    $pengaduanId,
        public readonly string $judul,
        public readonly string $namaPelapor,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'         => 'klarifikasi_masyarakat',
            'pengaduan_id' => $this->pengaduanId,
            'judul'        => $this->judul,
            'message'      => "{$this->namaPelapor} mengirim balasan pada pengaduan \"{$this->judul}\".",
            'url'          => route('petugas.dashboard'),
        ];
    }
}
