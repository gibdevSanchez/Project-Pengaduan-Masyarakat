<?php

namespace App\Console\Commands;

use App\Models\Pengaduan;
use Illuminate\Console\Command;

class AutoDeletePengaduan extends Command
{
    protected $signature = 'pengaduan:auto-delete';
    protected $description = 'Soft-delete complaints that have been selesai for 30+ days';

    public function handle(): void
    {
        $rows = Pengaduan::where('status', 'selesai')
            ->where('selesai_at', '<=', now()->subDays(30))
            ->whereNull('deleted_at')
            ->get();

        $count = $rows->count();
        $rows->each(fn($p) => $p->delete());

        $this->info("Auto-deleted {$count} pengaduan.");
    }
}
