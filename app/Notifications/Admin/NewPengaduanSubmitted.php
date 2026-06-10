<?php

namespace App\Notifications\Admin;

use Illuminate\Notifications\Notification;

class NewPengaduanSubmitted extends Notification
{
    public function __construct(
        public readonly int    $pengaduanId,
        public readonly string $judul,
        public readonly string $kategori,
        public readonly string $pelapor,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'         => 'new_pengaduan',
            'pengaduan_id' => $this->pengaduanId,
            'judul'        => $this->judul,
            'kategori'     => $this->kategori,
            'message'      => "Pengaduan baru dari {$this->pelapor}: \"{$this->judul}\" (kategori: {$this->kategori}).",
            'url'          => route('admin.pengaduan.index'),
        ];
    }
}
