<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Pengaduan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PengaduanController extends Controller
{
    public function updateStatus(Request $request, $id): JsonResponse
    {
        $request->validate(['status' => 'required|in:proses,selesai']);

        $pengaduan = Pengaduan::findOrFail($id);
        abort_if($pengaduan->id_petugas !== auth('petugas')->user()->id_petugas, 403);

        $pengaduan->update([
            'status'     => $request->status,
            'selesai_at' => $request->status === 'selesai' ? now() : $pengaduan->selesai_at,
        ]);

        return response()->json(['status' => $pengaduan->status]);
    }

    public function takedown(Request $request, $id): JsonResponse
    {
        $request->validate(['reason' => 'required|string|min:10']);

        $pengaduan = Pengaduan::findOrFail($id);
        abort_if($pengaduan->id_petugas !== auth('petugas')->user()->id_petugas, 403);
        abort_if($pengaduan->status === 'selesai', 409, 'Pengaduan yang sudah selesai tidak dapat di-takedown.');

        $pengaduan->update([
            'status'          => 'tidak_valid',
            'takedown_reason' => $request->reason,
        ]);

        return response()->json(['status' => 'tidak_valid']);
    }
}
