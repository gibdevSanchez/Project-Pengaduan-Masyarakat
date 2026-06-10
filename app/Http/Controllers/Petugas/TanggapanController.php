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
        $petugas   = auth('petugas')->user();

        abort_if(
            $pengaduan->id_petugas !== null && $pengaduan->id_petugas !== $petugas->id_petugas,
            403,
            'Pengaduan ini belum di-assign ke Anda.'
        );

        Tanggapan::create([
            'id_pengaduan' => $pengaduan->id_pengaduan,
            'tanggapan'    => $request->tanggapan,
            'id_petugas'   => $petugas->id_petugas,
        ]);

        $oldStatus = $pengaduan->status;
        $pengaduan->update([
            'status'     => $request->status,
            'selesai_at' => $request->status === 'selesai' ? now() : $pengaduan->selesai_at,
        ]);

        activity('pengaduan')
            ->causedBy($petugas)
            ->withProperties([
                'ip'          => $request->ip(),
                'id_pengaduan'=> $id,
                'dari'        => $oldStatus,
                'ke'          => $request->status,
            ])
            ->log("Status pengaduan #{$id} diubah: {$oldStatus} → {$request->status} oleh {$petugas->nama_petugas}");

        return redirect()->route('petugas.dashboard')
            ->with('success', 'Tanggapan berhasil dikirim dan status pengaduan diperbarui.');
    }

    public function assign($id)
    {
        $petugas   = auth('petugas')->user();
        $pengaduan = Pengaduan::whereNull('id_petugas')->findOrFail($id);
        $pengaduan->update(['id_petugas' => $petugas->id_petugas]);

        activity('pengaduan')
            ->causedBy($petugas)
            ->withProperties(['ip' => request()->ip(), 'id_pengaduan' => $id])
            ->log("Pengaduan #{$id} diambil oleh {$petugas->nama_petugas}");

        return redirect()->route('petugas.dashboard')
            ->with('success', 'Pengaduan berhasil diambil.');
    }
}
