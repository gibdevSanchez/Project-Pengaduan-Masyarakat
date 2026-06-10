<?php

namespace App\Console\Commands;

use App\Models\AppSetting;
use App\Models\Pengaduan;
use App\Models\Petugas;
use App\Notifications\Admin\SlaBreached as AdminSlaBreached;
use App\Notifications\Petugas\SlaNearBreach;
use App\Notifications\Petugas\SlaBreached as PetugasSlaBreached;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class CheckSlaNotifications extends Command
{
    protected $signature   = 'sla:check-notifications';
    protected $description = 'Send SLA near-breach and breach notifications to petugas and admin';

    public function handle(): int
    {
        $slaHours = [
            'keamanan'      => (int) AppSetting::get('sla_keamanan', '24'),
            'infrastruktur' => (int) AppSetting::get('sla_infrastruktur', '72'),
            'lingkungan'    => (int) AppSetting::get('sla_lingkungan', '48'),
            'sosial'        => (int) AppSetting::get('sla_sosial', '72'),
            'lainnya'       => (int) AppSetting::get('sla_lainnya', '72'),
        ];

        $admins = Petugas::where('level', 'admin')->get();

        $active = Pengaduan::whereIn('status', ['menunggu', 'proses'])
            ->with('petugasAssigned')
            ->get();

        $nearCount   = 0;
        $breachCount = 0;

        foreach ($active as $pengaduan) {
            $hours    = $slaHours[$pengaduan->kategori] ?? $slaHours['lainnya'];
            $deadline = $pengaduan->created_at->addHours($hours);

            // positive = time remaining (not overdue), negative = overdue
            $totalMinutes  = $hours * 60;
            $remainMinutes = (int) round(($deadline->timestamp - now()->timestamp) / 60);

            $isOverdue = $remainMinutes < 0;
            $pct       = $totalMinutes > 0 ? ($remainMinutes / $totalMinutes) : 0;

            $petugas = $pengaduan->petugasAssigned;

            if ($isOverdue) {
                // SLA breached — notify once per complaint
                if (is_null($pengaduan->sla_breach_notif_sent_at)) {
                    $lewat = (int) abs($remainMinutes);

                    if ($petugas) {
                        $petugas->notify(new PetugasSlaBreached(
                            $pengaduan->id_pengaduan,
                            Str::limit($pengaduan->isi_laporan, 50),
                            $lewat
                        ));
                    }

                    $petugasNama = $petugas?->nama_petugas ?? 'Belum ditugaskan';
                    foreach ($admins as $admin) {
                        $admin->notify(new AdminSlaBreached(
                            $pengaduan->id_pengaduan,
                            Str::limit($pengaduan->isi_laporan, 50),
                            $petugasNama,
                            $lewat
                        ));
                    }

                    $pengaduan->update(['sla_breach_notif_sent_at' => now()]);
                    $breachCount++;
                }
            } elseif ($pct <= 0.15 && is_null($pengaduan->sla_notif_sent_at)) {
                // 15% or less remaining — near breach alert (once per complaint)
                if ($petugas) {
                    $petugas->notify(new SlaNearBreach(
                        $pengaduan->id_pengaduan,
                        Str::limit($pengaduan->isi_laporan, 50),
                        (int) $remainMinutes
                    ));
                }

                $pengaduan->update(['sla_notif_sent_at' => now()]);
                $nearCount++;
            }
        }

        $this->info("SLA check selesai: {$nearCount} near-breach, {$breachCount} breach notifications sent.");
        return Command::SUCCESS;
    }
}
