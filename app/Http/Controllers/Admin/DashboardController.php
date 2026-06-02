<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengaduan;
use App\Models\Petugas;

class DashboardController extends Controller
{
    public function index()
    {
        $selesaiLast7 = Pengaduan::where('status', 'selesai')
            ->where('selesai_at', '>=', now()->subDays(7))
            ->count();

        $totalLast7 = Pengaduan::where('created_at', '>=', now()->subDays(7))
            ->where('status', '!=', 'tidak_valid')
            ->count();

        $completionRate = $totalLast7 > 0
            ? min(100, round(($selesaiLast7 / $totalLast7) * 100))
            : 0;

        // PHP-side grouping to stay DB-agnostic (works with both SQLite and MySQL)
        $weeklyData = Pengaduan::where('created_at', '>=', now()->subWeeks(8))
            ->get(['created_at'])
            ->groupBy(fn($p) => $p->created_at->format('Y-W'))
            ->map->count();

        $stats = [
            'total'         => Pengaduan::count(),
            'menunggu'      => Pengaduan::where('status', 'menunggu')->count(),
            'menungguToday' => Pengaduan::where('status', 'menunggu')
                ->whereDate('created_at', today())->count(),
            'proses'        => Pengaduan::where('status', 'proses')->count(),
            'petugasAktif'  => Pengaduan::where('status', 'proses')
                ->whereNotNull('id_petugas')
                ->distinct()->count('id_petugas'),
            'selesai'       => Pengaduan::where('status', 'selesai')->count(),
            'completionRate'=> $completionRate,
        ];

        $recentPengaduan = Pengaduan::with('masyarakat')->latest()->take(10)->get();

        $onlinePetugas = Petugas::where('level', 'petugas')
            ->whereNotNull('last_seen_at')
            ->where('last_seen_at', '>=', now()->subMinutes(5))
            ->get();

        return view('admin.dashboard', compact('stats', 'weeklyData', 'recentPengaduan', 'onlinePetugas'));
    }
}
