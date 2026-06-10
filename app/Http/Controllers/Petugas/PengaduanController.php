<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Pengaduan;
use App\Models\Petugas;
use App\Notifications\Admin\PetugasTakedown;
use App\Notifications\Masyarakat\PengaduanTakedown;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PengaduanController extends Controller
{
    public function updateStatus(Request $request, $id): JsonResponse
    {
        $request->validate(['status' => 'required|in:proses,selesai']);

        $petugas   = auth('petugas')->user();
        $pengaduan = Pengaduan::findOrFail($id);
        abort_if($pengaduan->id_petugas !== $petugas->id_petugas, 403);

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

        return response()->json(['status' => $pengaduan->status]);
    }

    public function takedown(Request $request, $id): JsonResponse
    {
        $request->validate(['reason' => 'required|string|min:10']);

        $petugas   = auth('petugas')->user();
        $pengaduan = Pengaduan::findOrFail($id);
        abort_if($pengaduan->id_petugas !== $petugas->id_petugas, 403);
        abort_if($pengaduan->status === 'selesai', 409, 'Pengaduan yang sudah selesai tidak dapat di-takedown.');

        $pengaduan->update([
            'status'          => 'tidak_valid',
            'takedown_reason' => $request->reason,
        ]);

        activity('pengaduan')
            ->causedBy($petugas)
            ->withProperties([
                'ip'          => $request->ip(),
                'id_pengaduan'=> $id,
                'alasan'      => $request->reason,
            ])
            ->log("Pengaduan #{$id} di-takedown oleh {$petugas->nama_petugas}: \"{$request->reason}\"");

        if ($pengaduan->masyarakat) {
            $pengaduan->masyarakat->notify(new PengaduanTakedown(
                (int) $id,
                Str::limit($pengaduan->isi_laporan, 50),
                $request->reason,
            ));
        }

        Petugas::where('level', 'admin')->get()->each(
            fn($admin) => $admin->notify(new PetugasTakedown(
                (int) $id,
                Str::limit($pengaduan->isi_laporan, 50),
                $petugas->nama_petugas,
                $request->reason,
            ))
        );

        return response()->json(['status' => 'tidak_valid']);
    }
}
