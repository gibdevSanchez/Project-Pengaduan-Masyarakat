<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Klarifikasi;
use App\Models\Pengaduan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class KlarifikasiController extends Controller
{
    public function index($id): JsonResponse
    {
        $petugas   = auth('petugas')->user();
        $pengaduan = Pengaduan::findOrFail($id);
        abort_if($pengaduan->id_petugas !== $petugas->id_petugas, 403);

        $messages = Klarifikasi::where('id_pengaduan', $id)
            ->orderBy('created_at')
            ->get()
            ->map(fn($k) => [
                'dari'       => $k->dari,
                'pesan'      => $k->pesan,
                'created_at' => $k->created_at->format('d M Y, H:i'),
            ]);

        return response()->json($messages);
    }

    public function store(Request $request, $id): JsonResponse
    {
        $request->validate(['pesan' => 'required|string|min:3']);

        $petugas   = auth('petugas')->user();
        $pengaduan = Pengaduan::findOrFail($id);

        abort_if($pengaduan->id_petugas !== $petugas->id_petugas, 403);
        abort_if(is_null($pengaduan->nik), 403, 'Pengaduan anonim tidak dapat dikomunikasikan.');
        abort_if(in_array($pengaduan->status, ['selesai', 'tidak_valid']), 409, 'Thread klarifikasi sudah ditutup.');

        $klarifikasi = Klarifikasi::create([
            'id_pengaduan' => $id,
            'pesan'        => $request->pesan,
            'dari'         => 'petugas',
            'id_pengirim'  => $petugas->id_petugas,
        ]);

        return response()->json([
            'dari'       => 'petugas',
            'pesan'      => $klarifikasi->pesan,
            'created_at' => $klarifikasi->created_at->format('d M Y, H:i'),
        ], 201);
    }
}
