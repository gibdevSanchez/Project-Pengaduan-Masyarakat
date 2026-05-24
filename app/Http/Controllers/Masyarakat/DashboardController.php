<?php

namespace App\Http\Controllers\Masyarakat;

use App\Http\Controllers\Controller;
use App\Models\Pengaduan;

class DashboardController extends Controller
{
    public function index()
    {
        $user      = auth('masyarakat')->user();
        $pengaduan = Pengaduan::with('tanggapan.petugas')
            ->where('masyarakat_id', auth('masyarakat')->user()->id)
            ->latest()
            ->get();

        return view('masyarakat.dashboard', compact('pengaduan', 'user'));
    }

    public function riwayat()
    {
        $pengaduan = Pengaduan::with('tanggapan.petugas')
            ->where('masyarakat_id', auth('masyarakat')->user()->id)
            ->latest()
            ->get();

        return view('masyarakat.riwayat', compact('pengaduan'));
    }
}
