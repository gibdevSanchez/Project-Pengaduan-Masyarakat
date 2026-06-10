<?php

namespace App\Notifications\Admin;

use Illuminate\Notifications\Notification;

class PetugasTakedown extends Notification
{
    public function __construct(
        public readonly int    $pengaduanId,
        public readonly string $judul,
        public readonly string $petugasNama,
        public readonly string $alasan,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'         => 'petugas_takedown',
            'pengaduan_id' => $this->pengaduanId,
            'judul'        => $this->judul,
            'message'      => "Petugas {$this->petugasNama} men-takedown pengaduan \"{$this->judul}\". Alasan: {$this->alasan}",
            'url'          => route('admin.pengaduan.index'),
        ];
    }
}
