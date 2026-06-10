<?php

namespace App\Notifications\Masyarakat;

use Illuminate\Notifications\Notification;

class PengaduanStatusUpdated extends Notification
{
    public function __construct(
        public readonly int    $pengaduanId,
        public readonly string $judul,
        public readonly string $statusBaru,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $label = match ($this->statusBaru) {
            'proses'      => 'Sedang Diproses',
            'selesai'     => 'Selesai',
            'tidak_valid' => 'Tidak Valid',
            default       => ucfirst($this->statusBaru),
        };

        return [
            'type'          => 'pengaduan_status',
            'pengaduan_id'  => $this->pengaduanId,
            'judul'         => $this->judul,
            'status'        => $this->statusBaru,
            'message'       => "Status pengaduan \"{$this->judul}\" diperbarui menjadi {$label}.",
            'url'           => route('masyarakat.pengaduan.show', $this->pengaduanId),
        ];
    }
}
