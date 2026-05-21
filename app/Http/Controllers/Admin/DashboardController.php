<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengaduan;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total'    => Pengaduan::count(),
            'menunggu' => Pengaduan::where('status', 'menunggu')->count(),
            'proses'   => Pengaduan::where('status', 'proses')->count(),
            'selesai'  => Pengaduan::where('status', 'selesai')->count(),
        ];

        $recentPengaduan = Pengaduan::with('masyarakat')->latest()->take(10)->get();

        return view('admin.dashboard', compact('stats', 'recentPengaduan'));
    }
}
