<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Pengaduan;
use App\Models\Tanggapan;
use Illuminate\Http\Request;

class TanggapanController extends Controller
{
    public function store(Request $request, $id)
    {
        $request->validate([
            'tanggapan' => 'required|string|min:3',
            'status'    => 'required|in:menunggu,proses,selesai',
        ]);

        $pengaduan = Pengaduan::findOrFail($id);

        abort_if(
            $pengaduan->id_petugas !== null && $pengaduan->id_petugas !== auth('petugas')->user()->id_petugas,
            403,
            'Pengaduan ini belum di-assign ke Anda.'
        );

        Tanggapan::create([
            'id_pengaduan'  => $pengaduan->id_pengaduan,
            'tgl_tanggapan' => now()->toDateString(),
            'tanggapan'     => $request->tanggapan,
            'id_petugas'    => auth('petugas')->id(),
        ]);

        $pengaduan->update([
            'status'     => $request->status,
            'selesai_at' => $request->status === 'selesai' ? now() : $pengaduan->selesai_at,
        ]);

        return redirect()->route('petugas.dashboard')
            ->with('success', 'Tanggapan berhasil dikirim dan status pengaduan diperbarui.');
    }

    public function assign($id)
    {
        $pengaduan = Pengaduan::whereNull('id_petugas')->findOrFail($id);
        $pengaduan->update(['id_petugas' => auth('petugas')->user()->id_petugas]);

        return redirect()->route('petugas.dashboard')
            ->with('success', 'Pengaduan berhasil diambil.');
    }
}
